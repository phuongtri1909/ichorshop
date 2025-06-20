<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'content',
        'status'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Mặc định trạng thái khi tạo đánh giá
    const STATUS_PUBLISHED = 'published';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';

    /**
     * Lấy thông tin người dùng đã đánh giá
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy thông tin sản phẩm được đánh giá
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Lấy thông tin đơn hàng liên quan
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Lấy tên người đánh giá dạng ẩn danh
     */
    public function getHiddenNameAttribute()
    {
        $firstName = $this->user ? $this->user->first_name : 'Anonymous';
        $lastName = $this->user ? $this->user->last_name : 'User';
        
        return $firstName . ' ' . substr($lastName, 0, 1) . '.';
    }

    /**
     * Scope để lấy các đánh giá đã được duyệt
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope để lấy các đánh giá mới nhất
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}