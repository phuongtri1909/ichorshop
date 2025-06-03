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
}
