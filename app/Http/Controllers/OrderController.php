<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\ProductWeight;
use Illuminate\Support\Facades\DB;
use App\Models\Province;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Xây dựng query với các filter
        $ordersQuery = Order::with(['customer', 'product', 'productWeight']);

        // Filter theo mã đơn hàng
        if ($request->filled('order_code')) {
            $ordersQuery->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        // Filter theo khách hàng (tên hoặc số điện thoại)
        if ($request->filled('customer')) {
            $customerKeyword = $request->customer;
            $ordersQuery->whereHas('customer', function ($query) use ($customerKeyword) {
                $query->where('first_name', 'like', '%' . $customerKeyword . '%')
                    ->orWhere('last_name', 'like', '%' . $customerKeyword . '%')
                    ->orWhere('phone', 'like', '%' . $customerKeyword . '%');
            });
        }

        // Filter theo trạng thái
        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->status);
        }

        // Filter theo thời gian
        if ($request->filled('date_from')) {
            $ordersQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $ordersQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // Sắp xếp theo ngày tạo mới nhất
        $ordersQuery->orderBy('created_at', 'desc');

        // Thực thi query và phân trang kết quả
        $orders = $ordersQuery->paginate(10);

        return view('admin.pages.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Load dữ liệu liên quan
        $order->load(['customer', 'product', 'productWeight']);

        return view('admin.pages.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        // Xóa đơn hàng
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được xóa thành công.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipping,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', "Trạng thái đơn hàng đã được cập nhật từ '$oldStatus' thành '$request->status'");
    }
}