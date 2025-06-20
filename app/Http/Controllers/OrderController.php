<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng của người dùng hiện tại
     */
    public function userOrders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('client.pages.account.orders', [
            'orders' => $orders,
            'breadcrumbItems' => [
                ['title' => 'Home', 'url' => route('home')],
                ['title' => 'My Account', 'url' => route('user.my.account')],
                ['title' => 'My Orders', 'url' => null, 'active' => true]
            ]
        ]);
    }

    /**
     * Hiển thị chi tiết một đơn hàng cụ thể
     */
    public function userOrderDetail(Order $order)
    {
        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        // Eager load các relationships cần thiết
        $order->load(['items.product', 'items.productVariant', 'coupon']);

        return view('client.pages.account.order-detail', [
            'order' => $order,
            'breadcrumbItems' => [
                ['title' => 'Home', 'url' => route('home')],
                ['title' => 'My Account', 'url' => route('user.my.account')],
                ['title' => 'My Orders', 'url' => route('user.orders')],
                ['title' => 'Order #' . $order->order_code, 'url' => null, 'active' => true]
            ]
        ]);
    }
}
