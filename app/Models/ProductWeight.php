<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWeight extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'weight',
        'original_price',
        'discount_percent',
        'discounted_price',
        'is_default',
        'is_active'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountedPriceAttribute()
    {
        return $this->original_price - ($this->original_price * ($this->discount_percent / 100));
    }
}
