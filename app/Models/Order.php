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

    /**
     * Lấy danh sách đánh giá liên quan đến đơn hàng
     */
    public function reviews()
    {
        return $this->hasMany(ReviewRating::class);
    }

    /**
     * Kiểm tra xem đơn hàng có thể được đánh giá không
     */
    public function canBeReviewed()
    {
        return $this->status === 'completed';
    }

    /**
     * Lấy danh sách sản phẩm trong đơn hàng mà người dùng có thể đánh giá
     */
    public function getReviewableProducts($userId)
    {
        if (!$this->canBeReviewed()) {
            return collect();
        }

        // Lấy các sản phẩm duy nhất trong đơn hàng (loại bỏ trùng lặp variant)
        $productIds = $this->items()->with('product')->get()->pluck('product.id')->unique();
        
        // Lấy các sản phẩm đã được đánh giá trong đơn hàng này
        $reviewedProductIds = ReviewRating::where('order_id', $this->id)
            ->where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();
        
        // Lọc các sản phẩm chưa được đánh giá
        $reviewableProductIds = $productIds->diff($reviewedProductIds);
        
        // Trả về danh sách sản phẩm đầy đủ
        return Product::whereIn('id', $reviewableProductIds)->get();
    }
}
