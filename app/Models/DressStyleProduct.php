<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DressStyleProduct extends Model
{
    protected $fillable = ['dress_style_id', 'product_id'];

    public function dressStyle()
    {
        return $this->belongsTo(DressStyle::class, 'dress_style_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
