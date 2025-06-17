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
            $cart = self::firstWhere('user_id', $userId);
            
            // Nếu có giỏ hàng theo session và khác với giỏ hàng theo user, hợp nhất chúng
            $sessionCart = self::firstWhere('session_id', $sessionId);
            if ($sessionCart && (!$cart || $sessionCart->id !== $cart->id)) {
                if (!$cart) {
                    // Nếu user chưa có giỏ hàng, chuyển giỏ hàng session thành giỏ hàng user
                    $sessionCart->update(['user_id' => $userId]);
                    return $sessionCart;
                } else {
                    // Hợp nhất giỏ hàng
                    self::mergeCarts($sessionCart, $cart);
                    $sessionCart->delete();
                }
            }
            
            // Trả về giỏ hàng của user hoặc tạo mới
            return $cart ?: self::create([
                'user_id' => $userId,
                'session_id' => $sessionId
            ]);
        }

        // Nếu user chưa đăng nhập, lấy giỏ hàng theo session
        return self::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Hợp nhất hai giỏ hàng
     */
    private static function mergeCarts($sourceCart, $targetCart)
    {
        foreach ($sourceCart->items as $item) {
            $existingItem = $targetCart->items()
                ->where('product_variant_id', $item->product_variant_id)
                ->first();
            
            if ($existingItem) {
                // Nếu đã có sản phẩm trong giỏ hàng mục tiêu, tăng số lượng
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $item->quantity
                ]);
            } else {
                // Nếu chưa có, tạo mới item trong giỏ hàng mục tiêu
                $targetCart->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ]);
            }
        }

        // Cập nhật tổng giá trị giỏ hàng
        $targetCart->updateTotalPrice();
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
        $total = $this->items->sum('subtotal');
        $this->update(['total_price' => $total]);
        return $this;
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
