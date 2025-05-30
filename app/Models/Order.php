<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'product_weight_id',
        'quantity',
        'shipping_fee',
        'total',
        'status',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productWeight()
    {
        return $this->belongsTo(ProductWeight::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    const STATUS_PENDING = 'pending';     // Chờ xác nhận
    const STATUS_PROCESSING = 'processing'; // Đang xử lý
    const STATUS_SHIPPING = 'shipping';   // Đang giao hàng
    const STATUS_COMPLETED = 'completed'; // Hoàn thành
    const STATUS_CANCELLED = 'cancelled'; // Đã hủy
}
