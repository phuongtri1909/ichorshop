<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DressStyle extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'banner'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'dress_style_products');
    }
}
