<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_products');
    }

     public function getActiveProductsCountAttribute()
    {
        return $this->products()->whereNull('deleted_at')->count();
    }

    // URL friendly route
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
