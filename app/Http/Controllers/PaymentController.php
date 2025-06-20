<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;
    
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    /**
     * Process payment for an order
     */
    public function processPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        if ($order->status_payment === 'completed') {
            return redirect()->route('user.checkout.success', ['order' => $order->id])
                            ->with('info', 'Payment has already been processed for this order');
        }
        
        try {
            $result = $this->paymentService->processPayment($order, $order->payment_method);
            
            if ($result['success']) {
                // Xóa mã giảm giá khỏi session nếu có
                session()->forget('appliedCoupon');
                  // Xóa session checkout
                session()->forget('checkout_items');
                return redirect($result['redirect_url']);
            } else {
                return redirect()->route('user.checkout.address')
                                ->with('error', $result['message'] ?? 'Payment processing failed');
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('user.checkout.address')
                            ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle PayPal payment success
     */
    public function paypalSuccess(Request $request)
    {
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');
        $orderId = $request->input('order_id');
        
        if (!$paymentId || !$payerId || !$orderId) {
            return redirect()->route('user.cart.index')
                            ->with('error', 'Invalid payment information');
        }
        
        // Get the order
        $order = Order::findOrFail($orderId);
        
        // Security check: verify the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        try {
            // Execute the payment
            $result = $this->paymentService->executePaypalPayment($paymentId, $payerId, $order);
            
            if ($result['success']) {
                return redirect()->route('user.checkout.success', ['order' => $order->id])
                                ->with('success', 'Payment completed successfully');
            } else {
                return redirect()->route('user.checkout.address')
                                ->with('error', $result['message'] ?? 'Payment execution failed');
            }
        } catch (\Exception $e) {
            Log::error('PayPal payment execution error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'payment_id' => $paymentId,
                'payer_id' => $payerId
            ]);
            
            return redirect()->route('user.checkout.address')
                            ->with('error', 'Payment execution failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle PayPal payment cancellation
     */
    public function paypalCancel(Request $request)
    {
        $orderId = $request->input('order_id');
        
        if (!$orderId) {
            return redirect()->route('user.cart.index')
                            ->with('error', 'Invalid order information');
        }
        
        // Get the order
        $order = Order::findOrFail($orderId);
        
        // Security check: verify the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        // Update order status
        $order->status_payment = 'cancelled';
        $order->status = 'cancelled';
        $order->save();
        
        return redirect()->route('user.cart.index')
                        ->with('info', 'Payment was cancelled');
    }
    
    /**
     * Handle PayPal IPN (Instant Payment Notification)
     */
    public function paypalIpn(Request $request)
    {
        // Process IPN data
        $ipnData = $request->all();
        
        try {
            $result = $this->paymentService->handlePaypalIPN($ipnData);
            
            if ($result) {
                return response('IPN Handled', 200);
            } else {
                return response('IPN Failed', 400);
            }
        } catch (\Exception $e) {
            Log::error('PayPal IPN handling error', [
                'error' => $e->getMessage(),
                'ipn_data' => $ipnData
            ]);
            
            return response('IPN Error: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle order refund (admin function)
     */
    public function refundOrder(Request $request, Order $order)
    {
        // Security check: only admins should access this
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }
        
        // Validate request
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:255'
        ]);
        
        $amount = $request->input('amount');
        $reason = $request->input('reason', 'Order refunded by admin');
        
        try {
            // Process refund based on payment method
            if ($order->payment_method === 'paypal') {
                $result = $this->paymentService->refundPaypalPayment($order, $amount, $reason);
            } else {
                // For credit card or other payment methods
                // In a real implementation, you would call the appropriate refund method
                
                // Mark order as refunded
                $order->status_payment = 'refunded';
                $order->status = 'cancelled';
                $order->refund_amount = $amount ?? $order->total_amount;
                $order->refund_reason = $reason;
                $order->refunded_at = now();
                $order->save();
                
                $result = ['success' => true, 'message' => 'Refund processed successfully'];
            }
            
            if ($result['success']) {
                return redirect()->back()
                                ->with('success', 'Refund processed successfully');
            } else {
                return redirect()->back()
                                ->with('error', $result['message'] ?? 'Refund processing failed');
            }
        } catch (\Exception $e) {
            Log::error('Refund processing error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                            ->with('error', 'Refund processing failed: ' . $e->getMessage());
        }
    }
}