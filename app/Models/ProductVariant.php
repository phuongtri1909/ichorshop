<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'color',
        'color_name',
        'size',
        'price',
        'sku',
        'quantity',
        'status',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

}
