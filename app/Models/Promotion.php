<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = ['name', 'type', 'value', 'start_date', 'end_date', 'status', 'description', 'min_order_amount', 'max_discount_amount', 'usage_limit'];

    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'status' => 'boolean',
        'usage_limit' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    public static function getDiscountTypes()
    {
        return [
            self::TYPE_PERCENTAGE => 'Phần trăm (%)',
            self::TYPE_FIXED => 'Số tiền cố định ($)'
        ];
    }

    public function getFormattedDiscountValue()
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return $this->value . '%';
        } else {
            return '$' . number_format($this->value, 2);
        }
    }
}
