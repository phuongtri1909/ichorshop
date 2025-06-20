<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function getAvailableCoupons()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Get selected cart items for checkout
        $selectedItemIds = session('checkout_items', []);
        if (empty($selectedItemIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No items selected for checkout'
            ], 400);
        }

        $cart = Cart::getOrCreateCart($user->id);
        $selectedItems = $cart->items()
            ->with(['variant', 'product'])
            ->whereIn('id', $selectedItemIds)
            ->get();

        if ($selectedItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Selected items not found'
            ], 400);
        }

        // Only get coupons that are:
        // 1. Active
        // 2. Within date range
        // 3. Assigned to this user OR not assigned to any user (global coupons)
        // 4. Not exceeded global usage limit
        $now = now();
        $validCoupons = Coupon::where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhereRaw('usage_count < usage_limit');
            })
            ->where(function ($query) use ($user) {
                // Coupons specifically for this user OR global coupons (no user restrictions)
                $query->whereDoesntHave('users') // Global coupons (no user restrictions)
                    ->orWhereHas('users', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->get();

        $availableCoupons = [];

        foreach ($validCoupons as $coupon) {
            // Skip if user has exceeded personal usage limit
            if (!$coupon->userCanUse($user->id)) {
                continue;
            }

            // 4. Lọc ra những items có thể áp dụng mã giảm giá
            $eligibleItems = $selectedItems->filter(function ($item) use ($coupon) {
                return $coupon->appliesToVariant($item->product_variant_id);
            });

            $hasApplicableItems = !$eligibleItems->isEmpty();
            $invalidReason = '';

            // If no applicable items, set reason but still display
            if (!$hasApplicableItems) {
                $invalidReason = 'This coupon cannot be applied to any of the selected products.';
            }

            // 5. Tính tổng tiền của các sản phẩm đủ điều kiện
            $eligibleSubtotal = $eligibleItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            // 6. Kiểm tra đơn hàng tối thiểu
            $meetsMinimumRequirement = $coupon->meetsMinimumRequirement($eligibleSubtotal);
            if (!$meetsMinimumRequirement && $hasApplicableItems) {
                $minAmount = number_format($coupon->min_order_amount, 2);
                $invalidReason = "Minimum order amount of $$minAmount is required for eligible products.";
            }

            // Calculate discount only if all criteria are met
            $isEligible = $hasApplicableItems && $meetsMinimumRequirement;
            $discount = $isEligible ? $coupon->calculateDiscount($eligibleSubtotal) : 0;

            // Add to available coupons list
            $availableCoupons[] = [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'display_value' => $coupon->getDisplayValueAttribute(),
                'description' => $coupon->description,
                'discount' => round($discount, 2),
                'min_order_amount' => $coupon->min_order_amount,
                'applicable_items' => $eligibleItems->count(),
                'total_items' => $selectedItems->count(),
                'end_date' => $coupon->end_date ? $coupon->end_date->format('Y-m-d') : null,
                'is_eligible' => $isEligible,
                'ineligibility_reason' => $isEligible ? '' : $invalidReason
            ];
        }

        return response()->json([
            'success' => true,
            'coupons' => $availableCoupons
        ]);
    }

    /**
     * Áp dụng mã giảm giá vào giỏ hàng
     */
    public function apply(Request $request)
    {
        $code = $request->input('code');

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a coupon code.'
            ], 400);
        }

        $coupon = \App\Models\Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.'
            ], 404);
        }

        $user = Auth::user();

        // 1. Kiểm tra mã giảm giá còn hiệu lực không (active, thời gian, usage_limit)
        if (!$coupon->isValid()) {
            $reason = $this->getCouponInvalidReason($coupon);
            return response()->json([
                'success' => false,
                'message' => $reason
            ], 400);
        }

        // 2. Kiểm tra người dùng có được phép sử dụng mã giảm giá không
        if (!$coupon->isValidForUser($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is not available for your account.'
            ], 400);
        }

        // 3. Kiểm tra đã sử dụng hết số lần cho phép chưa
        if (!$coupon->userCanUse($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached the maximum usage limit for this coupon.'
            ], 400);
        }

        // 4. Lấy danh sách cart items đã chọn từ session
        $selectedItemIds = session('checkout_items', []);

        \Log::info("Selected item IDs for checkout", ["item_ids" => $selectedItemIds]);

        if (empty($selectedItemIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No items selected for checkout'
            ], 400);
        }

        $cart = Cart::getOrCreateCart($user->id);

        // 5. Lấy các sản phẩm đã chọn với thông tin chi tiết
        $selectedItems = $cart->items()
            ->with(['variant', 'product'])
            ->whereIn('id', $selectedItemIds)
            ->get();

        if ($selectedItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Selected items not found in cart'
            ], 400);
        }

        // 6. Lọc ra những items có thể áp dụng mã giảm giá
        $eligibleItems = $selectedItems->filter(function ($item) use ($coupon) {
            return $coupon->appliesToVariant($item->product_variant_id);
        });

        if ($eligibleItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon cannot be applied to any of the selected products.'
            ], 400);
        }

        // 7. Tính tổng tiền của các sản phẩm đủ điều kiện
        $eligibleSubtotal = $eligibleItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // 8. Kiểm tra đơn hàng tối thiểu (dựa trên tổng tiền của items đủ điều kiện)
        if (!$coupon->meetsMinimumRequirement($eligibleSubtotal)) {
            $minAmount = number_format($coupon->min_order_amount, 2);
            return response()->json([
                'success' => false,
                'message' => "Minimum order amount of $$minAmount is required for eligible products to use this coupon."
            ], 400);
        }

        // 9. Tính giảm giá cho các sản phẩm đủ điều kiện
        $totalDiscount = $coupon->calculateDiscount($eligibleSubtotal);

        // 10. Tính chi tiết giảm giá cho từng item
        $itemDiscounts = [];
        $totalEligibleAmount = $eligibleSubtotal;

        foreach ($eligibleItems as $item) {
            $itemTotal = $item->price * $item->quantity;
            $itemDiscountRatio = $itemTotal / $totalEligibleAmount;
            $itemDiscount = $totalDiscount * $itemDiscountRatio;

            $itemDiscounts[] = [
                'item_id' => $item->id,
                'product_name' => $item->product->name,
                'variant_info' => $item->variant->color_name . ' - ' . $item->variant->size,
                'original_amount' => $itemTotal,
                'discount_amount' => round($itemDiscount, 2),
                'final_amount' => $itemTotal - round($itemDiscount, 2)
            ];
        }

        // 11. Lưu thông tin vào session
        session([
            'appliedCoupon' => [
                'id' => $coupon->id,
                'code' => $code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'total_discount' => round($totalDiscount, 2),
                'eligible_items_count' => $eligibleItems->count(),
                'total_items_count' => $selectedItems->count(),
                'eligible_subtotal' => $eligibleSubtotal,
                'item_discounts' => $itemDiscounts,
                'min_order_amount' => $coupon->min_order_amount,
                'applied_at' => now()->toISOString()
            ]
        ]);

        // 12. Tạo thông báo chi tiết
        $message = "Coupon applied successfully! ";
        if ($eligibleItems->count() < $selectedItems->count()) {
            $notEligibleCount = $selectedItems->count() - $eligibleItems->count();
            $message .= "Applied to {$eligibleItems->count()} out of {$selectedItems->count()} selected products. ";
            $message .= "{$notEligibleCount} products are not eligible for this coupon.";
        } else {
            $message .= "Applied to all selected products.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'discount' => round($totalDiscount, 2),
            'code' => $code,
            'eligible_items_count' => $eligibleItems->count(),
            'total_items_count' => $selectedItems->count(),
            'eligible_subtotal' => $eligibleSubtotal,
            'item_discounts' => $itemDiscounts
        ]);
    }

    /**
     * Xóa mã giảm giá khỏi giỏ hàng
     */
    public function remove()
    {
        session()->forget('appliedCoupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully'
        ]);
    }

    /**
     * Lấy lý do mã giảm giá không hợp lệ
     */
    private function getCouponInvalidReason(Coupon $coupon)
    {
        $now = \Carbon\Carbon::now();

        if (!$coupon->is_active) {
            return 'This coupon is currently inactive.';
        }

        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return 'This coupon is not yet active. Valid from: ' . $coupon->start_date->format('M d, Y');
        }

        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return 'This coupon has expired on: ' . $coupon->end_date->format('M d, Y');
        }

        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            return 'This coupon has reached its maximum usage limit.';
        }

        return 'This coupon is not valid.';
    }

    /**
     * Lấy thông tin chi tiết mã giảm giá đã áp dụng
     */
    public function getAppliedCouponInfo()
    {
        $appliedCoupon = session('appliedCoupon');

        if (!$appliedCoupon) {
            return response()->json([
                'success' => false,
                'message' => 'No coupon applied'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'coupon' => $appliedCoupon
        ]);
    }
}
