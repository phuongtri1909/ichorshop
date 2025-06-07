<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = ['product_id','color', 'image_path', 'image_path_medium'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        return null;
    }

    public function getImageMediumUrlAttribute()
    {
        if ($this->image_path_medium) {
            return Storage::url($this->image_path_medium);
        }
        return null;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            // Delete image files when record is deleted
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
            if ($image->image_path_medium) {
                Storage::disk('public')->delete($image->image_path_medium);
            }
        });
    }
}