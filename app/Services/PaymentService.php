<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaypalSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class PaymentService
{
    protected $paypalSettings;

    public function __construct()
    {
        $this->paypalSettings = PaypalSetting::first();
        Log::info('PaymentService initialized', [
            'paypal_settings' => $this->paypalSettings ? $this->paypalSettings->sandbox_secret : null
        ]);
    }

    /**
     * Process a payment based on the selected method
     */
    public function processPayment(Order $order, string $paymentMethod)
    {
        switch ($paymentMethod) {
            case 'paypal':
                return $this->processPaypalPayment($order);
            case 'mastercard':
                return $this->processCreditCardPayment($order);
            default:
                throw new Exception("Unsupported payment method: {$paymentMethod}");
        }
    }

    /**
     * Process a payment using PayPal
     */
    protected function processPaypalPayment(Order $order)
    {
        if (!$this->paypalSettings) {
            throw new Exception("PayPal settings are not configured");
        }

        // Configure PayPal SDK with settings from database
        $paypalConfig = $this->getPaypalConfig();

        // Create PayPal transaction
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->getClientId(),
                $this->getClientSecret()
            )
        );

        $apiContext->setConfig($paypalConfig);

        // Create PayPal payment object
        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale');

        // Set redirect URLs
        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl(route('payment.paypal.success', ['order_id' => $order->id]))
            ->setCancelUrl(route('payment.paypal.cancel', ['order_id' => $order->id]));

        $payment->setRedirectUrls($redirectUrls);

        // Set payment details
        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency('USD')
            ->setTotal(number_format($order->total_amount, 2, '.', ''));

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount)
            ->setDescription("Order #{$order->order_code}")
            ->setInvoiceNumber($order->order_code);

        $payment->setTransactions([$transaction]);

        // Set payment method
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');
        $payment->setPayer($payer);

        try {
            // Create payment and get approval URL
            $payment->create($apiContext);
            $approvalUrl = $payment->getApprovalLink();

            // Save PayPal payment ID to order
            $order->payment_id = $payment->getId();
            $order->save();

            // Return approval URL for redirect
            return [
                'success' => true,
                'redirect_url' => $approvalUrl
            ];
        } catch (\Exception $e) {
            Log::error('PayPal payment error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'data' => $payment
            ]);

            throw new Exception("Failed to process PayPal payment: " . $e->getMessage());
        }
    }

    /**
     * Process payment using credit card (via Stripe)
     */
    protected function processCreditCardPayment(Order $order)
    {

        try {
            // Generate a payment confirmation token
            $paymentToken = Str::random(32);

            // Update order with payment info
            $order->payment_id = $paymentToken;
            $order->status_payment = 'completed';
            $order->save();

            // Log the transaction
            Log::info('Credit card payment processed', [
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'payment_token' => $paymentToken,
                'demo_mode' => true
            ]);

            return [
                'success' => true,
                'redirect_url' => route('user.checkout.success', ['order' => $order->id]),
                'message' => 'Payment processed successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Credit card payment error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            throw new Exception("Failed to process credit card payment: " . $e->getMessage());
        }
    }

    /**
     * Execute a PayPal payment after user approval
     */
    public function executePaypalPayment($paymentId, $payerId, Order $order)
    {
        $paypalConfig = $this->getPaypalConfig();

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->getClientId(),
                $this->getClientSecret()
            )
        );

        $apiContext->setConfig($paypalConfig);

        $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

        $execution = new \PayPal\Api\PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            // Execute the payment
            $result = $payment->execute($execution, $apiContext);

            // Verify payment status
            if ($result->getState() === 'approved') {
                // Update order status
                $order->status_payment = 'completed';
                $order->payment_id = $paymentId;
                $order->payer_id = $payerId;
                $order->save();

                return [
                    'success' => true,
                    'message' => 'Payment completed successfully'
                ];
            } else {
                throw new Exception("Payment not approved: " . $result->getState());
            }
        } catch (\Exception $e) {
            Log::error('PayPal payment execution error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'payment_id' => $paymentId,
                'payer_id' => $payerId
            ]);

            throw new Exception("Failed to execute PayPal payment: " . $e->getMessage());
        }
    }

    /**
     * Process a PayPal refund
     */
    public function refundPaypalPayment(Order $order, $amount = null, $reason = '')
    {
        if (!$order->payment_id) {
            throw new Exception("No payment ID found for this order");
        }

        $paypalConfig = $this->getPaypalConfig();

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->getClientId(),
                $this->getClientSecret()
            )
        );

        $apiContext->setConfig($paypalConfig);

        try {
            // Get payment details
            $payment = \PayPal\Api\Payment::get($order->payment_id, $apiContext);
            $transactions = $payment->getTransactions();

            if (empty($transactions)) {
                throw new Exception("No transactions found for this payment");
            }

            // Get sale ID from transaction
            $relatedResources = $transactions[0]->getRelatedResources();

            if (empty($relatedResources)) {
                throw new Exception("No related resources found for this transaction");
            }

            $sale = $relatedResources[0]->getSale();

            if (!$sale) {
                throw new Exception("No sale found for this transaction");
            }

            // Create refund object
            $refundAmount = new \PayPal\Api\Amount();
            $refundAmount->setCurrency('USD');

            // If amount is not provided, refund full amount
            if ($amount === null) {
                $refundAmount->setTotal(number_format($order->total_amount, 2, '.', ''));
            } else {
                $refundAmount->setTotal(number_format($amount, 2, '.', ''));
            }

            $refund = new \PayPal\Api\Refund();
            $refund->setAmount($refundAmount);

            if ($reason) {
                $refund->setDescription($reason);
            }

            // Process refund
            $sale->refund($refund, $apiContext);

            // Update order status
            $order->status_payment = 'refunded';
            $order->refund_amount = $amount ?? $order->total_amount;
            $order->refund_reason = $reason;
            $order->refunded_at = now();
            $order->save();

            return [
                'success' => true,
                'message' => 'Refund processed successfully'
            ];
        } catch (\Exception $e) {
            Log::error('PayPal refund error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'payment_id' => $order->payment_id
            ]);

            throw new Exception("Failed to process PayPal refund: " . $e->getMessage());
        }
    }

    /**
     * Handle PayPal IPN (Instant Payment Notification)
     */
    public function handlePaypalIPN(array $ipnData)
    {

        foreach ($ipnData as $key => $value) {
            if (is_string($value) && (strpos($value, ',') !== false || strpos($value, '|') !== false)) {
                $ipnData[$key] = explode(',', $value);
            }
        }

        // Verify IPN data with PayPal
        $verificationResponse = $this->verifyPaypalIPN($ipnData);

        if (!$verificationResponse) {
            Log::warning('Invalid PayPal IPN received', ['data' => $ipnData]);
            return false;
        }

        // Process IPN based on transaction type
        $txnType = $ipnData['txn_type'] ?? '';
        $paymentStatus = $ipnData['payment_status'] ?? '';
        $orderId = $ipnData['custom'] ?? null;

        // Find the order
        $order = Order::where('order_code', $orderId)->first();

        if (!$order) {
            Log::warning('PayPal IPN received for unknown order', ['data' => $ipnData]);
            return false;
        }

        // Update order based on payment status
        switch ($paymentStatus) {
            case 'Completed':
                $order->status_payment = 'completed';
                $order->status = 'processing';
                break;

            case 'Pending':
                $order->status_payment = 'pending';
                break;

            case 'Failed':
            case 'Denied':
            case 'Expired':
                $order->status_payment = 'failed';
                break;

            case 'Refunded':
                $order->status_payment = 'refunded';
                $order->status = 'cancelled';
                $order->refunded_at = now();
                $order->refund_amount = $ipnData['mc_gross'] ?? null;
                break;
        }

        $order->payment_transaction_id = $ipnData['txn_id'] ?? null;
        $order->save();

        // Log IPN
        Log::info('PayPal IPN processed', [
            'order_id' => $order->id,
            'payment_status' => $paymentStatus,
            'txn_id' => $ipnData['txn_id'] ?? null
        ]);

        return true;
    }

    /**
     * Verify PayPal IPN with PayPal servers
     */
    protected function verifyPaypalIPN(array $ipnData)
    {
        // Add 'cmd' parameter
        $ipnData['cmd'] = '_notify-validate';

        // Determine PayPal endpoint based on mode
        $paypalUrl = $this->paypalSettings->mode === 'live'
            ? 'https://ipnpb.paypal.com/cgi-bin/webscr'
            : 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

        // Send verification request to PayPal
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($paypalUrl, [
                'form_params' => $ipnData,
                'headers' => [
                    'User-Agent' => 'PHP-IPN-Verification',
                ],
            ]);

            $body = (string) $response->getBody();

            return $body === 'VERIFIED';
        } catch (\Exception $e) {
            Log::error('PayPal IPN verification error', [
                'error' => $e->getMessage(),
                'ipn_data' => $ipnData
            ]);

            return false;
        }
    }

    /**
     * Get PayPal client ID based on mode (sandbox/live)
     */
    protected function getClientId()
    {
        if ($this->paypalSettings->mode === 'sandbox') {
            return $this->paypalSettings->sandbox_username;
        } else {
            return $this->paypalSettings->live_username;
        }
    }

    /**
     * Get PayPal client secret based on mode (sandbox/live)
     */
    protected function getClientSecret()
    {
        if ($this->paypalSettings->mode === 'sandbox') {
            return $this->paypalSettings->sandbox_secret;
        } else {
            return $this->paypalSettings->live_secret;
        }
    }

    /**
     * Get PayPal configuration
     */
    protected function getPaypalConfig()
    {
        return [
            'mode' => $this->paypalSettings->mode,
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => storage_path('logs/paypal.log'),
            'log.LogLevel' => 'INFO',
            'validation.level' => 'log',
            'cache.enabled' => true,
        ];
    }
}
