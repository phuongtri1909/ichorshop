<?php

namespace App\Mail;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CouponMail extends Mailable
{
    use Queueable, SerializesModels;

    public $coupon;
    public $user;
    public $expiry;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Coupon $coupon, User $user)
    {
        $this->coupon = $coupon;
        $this->user = $user;
        $this->expiry = $coupon->end_date ? $coupon->end_date->format('d/m/Y') : 'không giới hạn';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('Building coupon email for user: ' . $this->user->id . ', coupon: ' . $this->coupon->code);
        return $this->subject('Mã giảm giá đặc biệt dành riêng cho bạn!')
            ->markdown('emails.coupon')
            ->with([
                'userName' => $this->user->full_name,
                'couponCode' => $this->coupon->code,
                'discount' => $this->coupon->display_value,
                'minOrder' => $this->coupon->min_order_amount > 0 ? number_format($this->coupon->min_order_amount, 2) : null,
                'expiry' => $this->expiry,
                'description' => $this->coupon->description
            ]);
    }
}
