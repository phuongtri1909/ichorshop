<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'highlight',
        'image',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'highlight' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function weights()
    {
        return $this->hasMany(ProductWeight::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_id');
    }

    // Tính trung bình đánh giá
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    // Đếm số đánh giá
    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }

    // Lấy quy cách mặc định
    public function getDefaultWeightAttribute()
    {
        return $this->weights()->where('is_default', true)->first() 
            ?? $this->weights()->first();
    }
    
    // Lấy giá thấp nhất
    public function getMinPriceAttribute()
    {
        return $this->weights()->min('original_price') ?? 0;
    }
    
    // Lấy giá thấp nhất sau khuyến mãi
    public function getMinDiscountedPriceAttribute()
    {
        $weights = $this->weights()->get();
        if ($weights->isEmpty()) {
            return 0;
        }
        
        return $weights->map(function($weight) {
            return $weight->discounted_price;
        })->min();
    }
}