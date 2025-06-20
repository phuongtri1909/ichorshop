<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
        'order_code',
        'phone',
        'email',
        'first_name',
        'last_name',
        'country',
        'state',
        'city',
        'address',
        'postal_code',
        'apt',
        'status',
        'total_amount',
        'payment_method',
        'status_payment',
        'admin_notes',
        'user_notes'
    ];

    /**
     * Lấy các item của đơn hàng
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Lấy thông tin người dùng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy thông tin mã giảm giá
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
