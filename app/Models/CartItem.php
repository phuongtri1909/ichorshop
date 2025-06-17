<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'product_variant_id', 'quantity', 'price'];

    /**
     * Giỏ hàng chứa item này
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Sản phẩm của item này
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Biến thể sản phẩm của item này
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Lấy tổng tiền của item này
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Lấy thông tin khuyến mãi nếu có
     */
    public function getPromotionInfoAttribute()
    {
        $variant = $this->variant;
        $regularPrice = $variant->price;
        $discountedPrice = $this->price;

        if ($discountedPrice < $regularPrice) {
            $discount = round(100 - ($discountedPrice / $regularPrice * 100));
            return [
                'original_price' => $regularPrice,
                'discount_percentage' => $discount
            ];
        }

        return null;
    }

    public function getIsCheckableAttribute()
    {
        return $this->variant->quantity > 0;
    }
}
