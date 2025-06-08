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


    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'product_variant_promotions');
    }

    public function variantPromotions()
    {
        return $this->hasMany(ProductVariantPromotion::class, 'product_variant_id');
    }

    public function getActivePromotion()
    {
        $now = now();
        return $this->promotions()
            ->where('status', Promotion::STATUS_ACTIVE)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('value', 'desc')
            ->first();
    }

    public function getDiscountedPrice()
    {
        $promotion = $this->getActivePromotion();

        if (!$promotion) {
            return $this->price;
        }

        if ($promotion->type === Promotion::TYPE_PERCENTAGE) {
            $discount = $this->price * ($promotion->value / 100);
            if ($promotion->max_discount_amount > 0) {
                $discount = min($discount, $promotion->max_discount_amount);
            }
            return $this->price - $discount;
        } else {
            return max(0, $this->price - $promotion->value);
        }
    }
}
