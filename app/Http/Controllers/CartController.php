<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Countries;
use App\Models\OrderSetting;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng
     */
    public function index()
    {
        $cart = Cart::getOrCreateCart(Auth::id());
        $cart->load(['items.product', 'items.variant']);

        $groupedItems = $cart->items->groupBy('product_id')->sortBy(function ($group) {
            return -$group->max('id');
        });

        $sortedItems = collect([]);
        foreach ($groupedItems as $group) {
            $sortedItems = $sortedItems->merge($group->sortByDesc('id'));
        }

        $cart->setRelation('items', $sortedItems);

        return view('client.pages.account.cart', [
            'cart' => $cart,
            'breadcrumbItems' => [
                ['title' => 'Home', 'url' => route('home')],
                ['title' => 'Shopping Cart', 'url' => null, 'active' => true]
            ]
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'quantity.min' => 'The quantity must be at least 1.',
            'variant_id.exists' => 'The selected variant does not exist.',
            'product_id.exists' => 'The selected product does not exist.'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Kiểm tra tồn kho
            $variant = ProductVariant::findOrFail($request->variant_id);
            if ($variant->quantity < $request->quantity) {
                throw new \Exception('Insufficient product quantity. Only ' . $variant->quantity . ' items left.');
            }

            // Thêm vào giỏ hàng
            $cart = Cart::getOrCreateCart(Auth::id());
            $cart->addItem(
                $request->product_id,
                $request->variant_id,
                $request->quantity
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added to cart successfully!',
                    'cart_count' => $cart->total_items
                ]);
            }

            return redirect()->route('user.cart.index')->with('success', 'Product added to cart successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            $cart = Cart::getOrCreateCart(Auth::id());
            $cartItem = $cart->items()->findOrFail($id);

            // Kiểm tra tồn kho
            if ($request->quantity > 0) {
                $variant = $cartItem->variant;
                if ($variant->quantity < $request->quantity) {
                    throw new \Exception('Insufficient product quantity. Only ' . $variant->quantity . ' items left.');
                }
            }

            // Cập nhật số lượng
            $cart->updateItemQuantity($id, $request->quantity);
            $updatedItem = $cart->items()->find($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated successfully!',
                    'cart' => [
                        'total_price' => $cart->total_price,
                        'total_items' => $cart->total_items
                    ],
                    'item' => [
                        'subtotal' => $updatedItem ? $updatedItem->subtotal : 0
                    ]
                ]);
            }

            return redirect()->route('user.cart.index')->with('success', 'Cart updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove(Request $request, $id)
    {
        try {
            $cart = Cart::getOrCreateCart(Auth::id());
            $cart->removeItem($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from cart successfully!',
                    'cart' => [
                        'total_price' => $cart->total_price,
                        'total_items' => $cart->total_items
                    ]
                ]);
            }

            return redirect()->route('user.cart.index')->with('success', 'Product removed from cart successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Làm trống giỏ hàng
     */
    public function clear(Request $request)
    {
        $cart = Cart::getOrCreateCart(Auth::id());
        $cart->clear();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng!'
            ]);
        }

        return redirect()->route('user.cart.index')->with('success', 'Cart cleared successfully!');
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng
     */
    public function getCount()
    {
        $cart = Cart::getOrCreateCart(Auth::id());
        return response()->json([
            'count' => $cart->total_items
        ]);
    }

    /**
     * Chuyển từ giỏ hàng sang trang checkout
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'exists:cart_items,id'
        ], [
            'items.required' => 'Please select at least one item to checkout.',
            'items.array' => 'Invalid items format.',
            'items.*.exists' => 'One or more selected items do not exist in your cart.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid items selected');
        }

        // Lấy cart của user hiện tại
        $cart = Cart::getOrCreateCart(Auth::id());

        // Kiểm tra xem các items có thuộc về user hiện tại hay không
        $selectedItemIds = $request->items;
        $userItemIds = $cart->items()->pluck('id')->toArray();

        // Kiểm tra xem tất cả các selectedItemIds có nằm trong userItemIds không
        $invalidItems = array_diff($selectedItemIds, $userItemIds);

        if (!empty($invalidItems)) {
            return redirect()->back()->with('error', 'Unauthorized access to cart items');
        }

        // Lưu danh sách cart items đã chọn vào session
        session(['checkout_items' => $request->items]);

        // Redirect đến trang nhập thông tin địa chỉ
        return redirect()->route('user.checkout.address');
    }

    /**
     * Hiển thị trang nhập địa chỉ giao hàng và thanh toán
     */
    public function checkoutAddress()
    {
        $user = Auth::user();

        // Lấy danh sách cart items đã chọn từ session
        $selectedItemIds = session('checkout_items', []);

        if (empty($selectedItemIds)) {
            return redirect()->route('user.cart.index')->with('error', 'No items selected for checkout');
        }

        $cart = Cart::getOrCreateCart($user->id);

        // Lấy các sản phẩm đã chọn
        $selectedItems = $cart->items()->whereIn('id', $selectedItemIds)->get();

        if ($selectedItems->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Selected items not found');
        }

        // Tính tổng tiền sản phẩm đã chọn
        $subtotal = $selectedItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $shippingCost = OrderSetting::where('key', 'shipping_cost')->value('value') ?? 5.00;

        // Tính thuế (giả sử 10%)

        $tax_percentage = OrderSetting::where('key', 'tax_percentage')->value('value') ?? 10;
        $tax_percentage = $tax_percentage / 100; // Chuyển đổi sang tỷ lệ

        $tax = round($subtotal * $tax_percentage, 2);

        // Tổng cộng
        $total = $subtotal + $shippingCost + $tax;

        // Lấy địa chỉ mặc định hoặc địa chỉ mới nhất của user
        $defaultAddress = $user->addresses()->where('is_default', true)->first();
        $latestAddress = $user->addresses()->latest()->first();
        $address = $defaultAddress ?: $latestAddress;

        // Lấy danh sách địa chỉ của user
        $addresses = $user->addresses()->get();

        // Lấy danh sách quốc gia
        $countries = Countries::orderBy('name')->get();

        return view('client.pages.account.checkout', [
            'user' => $user,
            'selectedItems' => $selectedItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'tax' => $tax,
            'total' => $total,
            'address' => $address,
            'addresses' => $addresses,
            'countries' => $countries
        ]);
    }

    /**
     * Xử lý đơn hàng khi người dùng hoàn tất checkout
     */
    public function processCheckout(Request $request)
    {
        $user = Auth::user();

        // Xác thực form thanh toán
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'country' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'payment_method' => 'required|in:paypal,mastercard',
            'applied_coupon' => 'nullable|string|max:50',
        ], [
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a string.',
            'last_name.required' => 'Last name is required.',
            'last_name.string' => 'Last name must be a string.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'phone.required' => 'Phone number is required.',
            'address.required' => 'Address is required.',
            'country.required' => 'Country is required.',
            'state.required' => 'State is required.',
            'city.required' => 'City is required.',
            'postal_code.required' => 'Postal code is required.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Payment method must be either paypal or mastercard.',
            'applied_coupon.string' => 'Coupon code must be a string.',
            'applied_coupon.max' => 'Coupon code cannot exceed 50 characters.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Lấy danh sách cart items đã chọn từ session
            $selectedItemIds = session('checkout_items', []);

            if (empty($selectedItemIds)) {
                return redirect()->route('user.cart.index')->with('error', 'No items selected for checkout');
            }

            $cart = Cart::getOrCreateCart($user->id);
            $selectedItems = $cart->items()->whereIn('id', $selectedItemIds)->get();

            if ($selectedItems->isEmpty()) {
                return redirect()->route('user.cart.index')->with('error', 'Selected items not found');
            }

            // Tính toán giá trị đơn hàng
            $subtotal = $selectedItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            // Phí ship
            $shippingCost = OrderSetting::where('key', 'shipping_cost')->value('value') ?? 5.00;

            // Thuế
            $tax_percentage = OrderSetting::where('key', 'tax_percentage')->value('value') ?? 10;
            $tax_percentage = $tax_percentage / 100; // Chuyển đổi sang tỷ lệ
            $tax = round($subtotal * $tax_percentage, 2);

            // Tổng cộng ban đầu (chưa trừ giảm giá)
            $total = $subtotal + $shippingCost + $tax;

            // Xử lý mã giảm giá nếu có
            $couponCode = $request->applied_coupon;
            $coupon = null;
            $couponDiscount = 0;
            $couponId = null;

            if ($couponCode) {
                $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

                if (!$coupon) {
                    return redirect()->back()->with('error', 'Invalid coupon code')->withInput();
                }

                // Kiểm tra tính hợp lệ của mã giảm giá
                if (!$coupon->isValid()) {
                    return redirect()->back()->with('error', 'Coupon has expired or is not valid')->withInput();
                }

                // Kiểm tra người dùng có được phép sử dụng mã giảm giá không
                if (!$coupon->isValidForUser($user->id)) {
                    return redirect()->back()->with('error', 'This coupon cannot be used by your account')->withInput();
                }

                // Kiểm tra người dùng đã dùng hết lượt chưa
                if (!$coupon->userCanUse($user->id)) {
                    return redirect()->back()->with('error', 'You have used this coupon the maximum number of times')->withInput();
                }

                // Kiểm tra giá trị đơn hàng tối thiểu
                if (!$coupon->meetsMinimumRequirement($subtotal)) {
                    $minAmount = number_format($coupon->min_order_amount, 2);
                    return redirect()->back()->with('error', "Order minimum of $$minAmount is required for this coupon")->withInput();
                }

                // Kiểm tra áp dụng cho các sản phẩm trong giỏ hàng
                $isApplicable = false;
                foreach ($selectedItems as $item) {
                    if ($coupon->appliesToVariant($item->product_variant_id)) {
                        $isApplicable = true;
                        break;
                    }
                }

                if (!$isApplicable && $coupon->applies_to === 'specific') {
                    return redirect()->back()->with('error', "This coupon doesn't apply to any items in your cart")->withInput();
                }

                // Tính toán giá trị giảm giá
                $couponDiscount = $coupon->calculateDiscount($subtotal);
                $couponId = $coupon->id;

                // Trừ giảm giá từ tổng tiền
                $total -= $couponDiscount;
            }

            // Tạo order code
            $orderCode = 'ORDER-' . strtoupper(substr(md5(uniqid()), 0, 10));

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'coupon_id' => $couponId,
                'phone' => $request->phone,
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'apt' => $request->apt,
                'status' => 'pending',
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'status_payment' => 'pending',
                'user_notes' => $request->notes
            ]);

            // Thêm chi tiết đơn hàng
            foreach ($selectedItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ]);

                // Cập nhật số lượng tồn kho
                $variant = $item->variant;
                if ($variant) {
                    $variant->quantity -= $item->quantity;
                    $variant->save();
                }

                // Xóa sản phẩm khỏi giỏ hàng
                $item->delete();
            }

            if ($coupon) {
                \App\Models\CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => $couponDiscount,
                    'used_at' => now()
                ]);

                // Tăng số lượt đã sử dụng
                $coupon->increment('usage_count');
            }

            // Cập nhật giỏ hàng
            $cart->updateTotalPrice();



            DB::commit();
            return redirect()->route('payment.process', ['order' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log lỗi
            \Log::error('Checkout process failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'An error occurred while processing your order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị trang khi thanh toán thành công
     */
    public function checkoutSuccess(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id != Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.productVariant']);

        return view('client.pages.account.checkout-success', [
            'order' => $order
        ]);
    }
}
