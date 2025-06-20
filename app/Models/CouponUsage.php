<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'amount',
        'used_at'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];
    
    /**
     * Mối quan hệ với mã giảm giá
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    
    /**
     * Mối quan hệ với người dùng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Mối quan hệ với đơn hàng
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
