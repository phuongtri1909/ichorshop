<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPromotion extends Model
{
    protected $fillable = [
        'product_variant_id',
        'promotion_id',
    ];

    /**
     * Get the product variant that owns the promotion.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id')
                    ->withTrashed(); 
    }
    /**
     * Get the promotion that owns the product variant.
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }
}
