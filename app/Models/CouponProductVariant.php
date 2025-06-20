<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponProductVariant extends Model
{

    protected $table = 'coupon_product_variant';

    protected $fillable = [
        'coupon_id',
        'product_variant_id',
    ];
}
