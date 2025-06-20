<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'description',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'value' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
    ];

    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

    /**
     * Mối quan hệ với các biến thể sản phẩm áp dụng mã giảm giá
     */
    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'coupon_product_variant');
    }

    /**
     * Mối quan hệ với các người dùng được phép sử dụng mã giảm giá
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user');
    }

    /**
     * Mối quan hệ với lịch sử sử dụng mã giảm giá
     */
    public function usageHistory()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Kiểm tra mã giảm giá còn hiệu lực không
     */
    public function isValid()
    {
        $now = Carbon::now();

        // Kiểm tra trạng thái kích hoạt
        if (!$this->is_active) {
            return false;
        }

        // Kiểm tra thời gian hiệu lực
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        // Kiểm tra giới hạn sử dụng tổng
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Kiểm tra mã giảm giá có áp dụng cho sản phẩm này không
     */
    public function appliesToVariant($variantId)
    {
        // Nếu không có biến thể nào được chỉ định, áp dụng cho tất cả
        if ($this->productVariants->isEmpty()) {
            return true;
        }

        return $this->productVariants()->where('product_variant_id', $variantId)->exists();
    }

    /**
     * Kiểm tra mã giảm giá có áp dụng cho người dùng này không
     */
    public function appliesToUser($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Kiểm tra người dùng đã sử dụng hết số lần cho phép chưa
     */
    public function userCanUse($userId)
    {
        if (!$this->usage_limit_per_user) {
            return true;
        }

        $usageCount = $this->usageHistory()
            ->where('user_id', $userId)
            ->count();

        return $usageCount < $this->usage_limit_per_user;
    }

    /**
     * Tính toán giá trị giảm giá dựa trên loại mã giảm giá
     */
    public function calculateDiscount($amount)
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            $discount = $amount * ($this->value / 100);

            // Áp dụng giới hạn giảm giá tối đa nếu có
            if ($this->max_discount_amount > 0) {
                $discount = min($discount, $this->max_discount_amount);
            }
        } else {
            $discount = $this->value;
        }

        // Đảm bảo giá trị giảm giá không lớn hơn tổng tiền
        return min($discount, $amount);
    }

    /**
     * Record việc sử dụng mã giảm giá
     */
    public function use($userId, $orderId, $discountAmount)
    {
        // Tăng tổng số lần sử dụng
        $this->increment('usage_count');

        // Ghi lại vào lịch sử sử dụng
        return $this->usageHistory()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'amount' => $discountAmount,
            'used_at' => now(),
        ]);
    }

    /**
     * Kiểm tra xem đơn hàng có đạt yêu cầu tối thiểu không
     */
    public function meetsMinimumRequirement($orderAmount)
    {
        if ($this->min_order_amount > 0) {
            return $orderAmount >= $this->min_order_amount;
        }

        return true;
    }

    /**
     * Định dạng hiển thị giá trị mã giảm giá
     */
    public function getDisplayValueAttribute()
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return $this->value . '%';
        }

        return '$' . number_format($this->value, 2);
    }

    public function isValidForUser($userId = null)
    {
        if (!$this->isValid()) {
            return false;
        }

        if (!$userId) {
            return false;
        }

        return $this->users()->where('user_id', $userId)->exists();
    }

    public function getApplicableVariantIds()
    {
        if (!$this->relationLoaded('productVariants')) {
            $this->load('productVariants');
        }

        return $this->productVariants->pluck('id')->toArray();
    }

    public function calculateDiscountForItems($items)
    {
        $eligibleItems = collect($items)->filter(function ($item) {
            return $this->appliesToVariant($item['variant_id'] ?? $item['product_variant_id']);
        });

        if ($eligibleItems->isEmpty()) {
            return [
                'total_discount' => 0,
                'eligible_items' => [],
                'ineligible_items' => $items
            ];
        }

        $eligibleSubtotal = $eligibleItems->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });

        if (!$this->meetsMinimumRequirement($eligibleSubtotal)) {
            return [
                'total_discount' => 0,
                'eligible_items' => [],
                'ineligible_items' => $items,
                'error' => 'Minimum order requirement not met'
            ];
        }

        $totalDiscount = $this->calculateDiscount($eligibleSubtotal);

        $itemDiscounts = [];
        foreach ($eligibleItems as $item) {
            $itemTotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
            $itemDiscountRatio = $eligibleSubtotal > 0 ? $itemTotal / $eligibleSubtotal : 0;
            $itemDiscount = $totalDiscount * $itemDiscountRatio;

            $itemDiscounts[] = array_merge($item, [
                'original_amount' => $itemTotal,
                'discount_amount' => round($itemDiscount, 2),
                'final_amount' => $itemTotal - round($itemDiscount, 2)
            ]);
        }

        return [
            'total_discount' => round($totalDiscount, 2),
            'eligible_items' => $itemDiscounts,
            'ineligible_items' => collect($items)->filter(function ($item) {
                return !$this->appliesToVariant($item['variant_id'] ?? $item['product_variant_id']);
            })->values()->toArray()
        ];
    }

    /**
     * Kiểm tra mã giảm giá còn hiệu lực và được phép sử dụng cho biến thể và người dùng
     */
    public function isValidForVariantAndUser($variantId, $userId = null)
    {
        if (!$this->isValidForUser($userId)) {
            return false;
        }
        return $this->appliesToVariant($variantId);
    }
}
