<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description_long', 'description_short', 'brand_id', 'avatar', 'avatar_medium', 'status'];


    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function dressStyles()
    {
        return $this->belongsToMany(DressStyle::class, 'dress_style_products');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }
        return asset('assets/images/no-image.png');
    }

    public function getAvatarMediumUrlAttribute()
    {
        if ($this->avatar_medium) {
            return Storage::url($this->avatar_medium);
        }
        return asset('assets/images/no-image.png');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            // Delete images when product is deleted
            if ($product->avatar) {
                Storage::disk('public')->delete($product->avatar);
                Storage::disk('public')->delete($product->avatar_medium);
            }

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                Storage::disk('public')->delete($image->image_path_medium);
            }
        });
    }
}
