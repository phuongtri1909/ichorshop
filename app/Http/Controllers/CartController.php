<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ],[
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
}
