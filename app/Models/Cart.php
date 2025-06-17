<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['session_id', 'user_id', 'total_price'];

    /**
     * Lấy các sản phẩm trong giỏ hàng
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Lấy người dùng sở hữu giỏ hàng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tạo hoặc lấy giỏ hàng cho session/user hiện tại
     */
    public static function getOrCreateCart($userId = null)
    {
        $sessionId = session()->getId();

        // Nếu user đăng nhập, tìm giỏ hàng theo user_id
        if ($userId) {
            $userCart = self::where('user_id', $userId)->first();

            if ($userCart) {
                return $userCart;
            }

            // Kiểm tra giỏ hàng session hiện tại và chuyển đổi nếu có
            $sessionCart = self::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->first();

            if ($sessionCart) {
                // Chuyển đổi giỏ hàng khách thành giỏ hàng user
                $sessionCart->user_id = $userId;
                $sessionCart->save();
                return $sessionCart;
            }

            // Tạo giỏ hàng mới cho user
            $newCart = self::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'total_price' => 0
            ]);
            return $newCart;
        }

        // Nếu chưa đăng nhập, tìm giỏ hàng theo session
        $sessionCart = self::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if ($sessionCart) {
            return $sessionCart;
        }

        // Tạo giỏ hàng mới cho session
        $newCart = self::create([
            'session_id' => $sessionId,
            'total_price' => 0
        ]);
        return $newCart;
    }

    /**
     * Hợp nhất hai giỏ hàng
     */
    public static function mergeCarts($sourceCart, $targetCart)
    {
        try {
            // Đảm bảo cả hai giỏ hàng tồn tại
            if (!$sourceCart || !$targetCart) {
                return false;
            }

            // Đảm bảo items đã được load
            $sourceCart->load('items.variant');


            // Hợp nhất từng sản phẩm
            foreach ($sourceCart->items as $item) {
                $existingItem = $targetCart->items()
                    ->where('product_variant_id', $item->product_variant_id)
                    ->first();

                if ($existingItem) {
                    // Cập nhật số lượng nếu sản phẩm đã tồn tại
                    $newQuantity = $existingItem->quantity + $item->quantity;
                    $existingItem->update(['quantity' => $newQuantity]);

                   
                } else {
                    // Thêm sản phẩm mới nếu chưa có
                    $targetCart->items()->create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ]);

                }
            }

            // Cập nhật tổng giá trị giỏ hàng đích
            $targetCart->updateTotalPrice();

            // Xóa giỏ hàng nguồn sau khi đã hợp nhất
            $sourceCart->delete();

            return true;
        } catch (\Exception $e) {
            \Log::error('Error in mergeCarts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public static function getCurrentCart()
    {
        $userId = auth()->id();
        return self::getOrCreateCart($userId);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addItem($productId, $variantId, $quantity = 1)
    {
        // Lấy thông tin variant
        $variant = ProductVariant::findOrFail($variantId);

        // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
        $cartItem = $this->items()->where('product_variant_id', $variantId)->first();

        if ($cartItem) {
            // Nếu đã có, tăng số lượng
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity
            ]);
        } else {
            // Nếu chưa có, tạo mới
            $this->items()->create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $variant->getDiscountedPrice() // Lấy giá đã giảm
            ]);
        }

        // Cập nhật tổng giá trị giỏ hàng
        $this->updateTotalPrice();

        return $this;
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateItemQuantity($cartItemId, $quantity)
    {
        if ($quantity <= 0) {
            // Nếu số lượng <= 0, xóa sản phẩm khỏi giỏ hàng
            $this->items()->where('id', $cartItemId)->delete();
        } else {
            // Cập nhật số lượng
            $this->items()->where('id', $cartItemId)->update(['quantity' => $quantity]);
        }

        // Cập nhật tổng giá trị
        $this->updateTotalPrice();

        return $this;
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeItem($cartItemId)
    {
        $this->items()->where('id', $cartItemId)->delete();
        $this->updateTotalPrice();
        return $this;
    }

    /**
     * Làm trống giỏ hàng
     */
    public function clear()
    {
        $this->items()->delete();
        $this->updateTotalPrice();
        return $this;
    }

    /**
     * Cập nhật tổng giá trị giỏ hàng
     */
    public function updateTotalPrice()
    {
        try {
            $total = $this->items()->sum(\DB::raw('price * quantity'));
            $this->total_price = $total;
            $this->save();
            return $this;
        } catch (\Exception $e) {
            return $this;
        }
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Kiểm tra giỏ hàng trống
     */
    public function getIsEmptyAttribute()
    {
        return $this->items->count() === 0;
    }
}
