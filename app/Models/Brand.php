<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'description'];


    /**
     * Get the products associated with the brand
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::url($this->logo);
        }
        return null;
    }

    /**
     * Get brand route key name for URL routing
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Delete logo file when brand is deleted
        static::deleting(function ($brand) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
        });
    }
}
