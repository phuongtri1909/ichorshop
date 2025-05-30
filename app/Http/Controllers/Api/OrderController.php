<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Models\ProductWeight;
use App\Models\CustomerAddress;
use App\Services\ShippingService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }
    /**
     * Tạo đơn hàng mới
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'product_weight_id' => 'required|exists:product_weights,id',
            'quantity' => 'required|integer|min:1',
            'shipping_fee' => 'required|numeric|min:0',
            'shipping_verification' => 'required|string',

            'customer.firstName' => 'required|string|max:255',
            'customer.lastName' => 'required|string|max:255',
            'customer.email' => 'required|email|max:255',
            'customer.phone' => 'required|string|max:20',
            'customer.provinces_code' => 'required|string|exists:provinces,code',
            'customer.districts_code' => 'required|string|exists:districts,code',
            'customer.wards_code' => 'required|string|exists:wards,code',
            'customer.address' => 'required|string|max:500',
            'customer.note' => 'nullable|string|max:1000',
        ],
        [
            'product_id.required' => 'Sản phẩm không được để trống',
            'product_weight_id.required' => 'Trọng lượng không được để trống',
            'quantity.required' => 'Số lượng không được để trống',
            'shipping_fee.required' => 'Phí vận chuyển không được để trống',
            'shipping_verification.required' => 'Mã xác thực phí vận chuyển không được để trống',
            'customer.firstName.required' => 'Họ tên không được để trống',
            'customer.email.required' => 'Email không được để trống',
            'customer.phone.required' => 'Số điện thoại không được để trống',
            'customer.provinces_code.required' => 'Tỉnh/Thành phố không được để trống',
            'customer.districts_code.required' => 'Quận/Huyện không được để trống',
            'customer.wards_code.required' => 'Phường/Xã không được để trống',
            'customer.address.required' => 'Địa chỉ không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Kiểm tra mã xác thực phí vận chuyển
            $isShippingValid = $this->shippingService->verify(
                $request->customer['provinces_code'],
                $request->customer['districts_code'],
                $request->customer['wards_code'],
                $request->product_weight_id,
                $request->quantity,
                $request->shipping_fee,
                $request->shipping_verification
            );

            if (!$isShippingValid) {
                return response()->json(['message' => 'Phí vận chuyển không hợp lệ. Vui lòng thử lại.'], 400);
            }

            // Lấy thông tin sản phẩm và option trọng lượng
            $weight = ProductWeight::findOrFail($request->product_weight_id);
            $product = Product::findOrFail($request->product_id);

            if ($weight->product_id != $product->id) {
                return response()->json(['message' => 'Trọng lượng không thuộc sản phẩm này'], 400);
            }

            // Tính toán giá trị đơn hàng
            $subtotal = $weight->discounted_price * $request->quantity;
            $total = $subtotal + $request->shipping_fee;

            // Tạo đơn hàng trong transaction
            DB::beginTransaction();

            // Tạo đơn hàng
            $order = Order::create([
                'product_id' => $request->product_id,
                'product_weight_id' => $request->product_weight_id,
                'quantity' => $request->quantity,
                'subtotal' => $subtotal,
                'shipping_fee' => $request->shipping_fee,
                'total' => $total,
                'status' => 'pending',
                'note' => $request->customer['note'] ?? null,
            ]);

            $provinceInfo = Province::where('code', $request->customer['provinces_code'])->first();
            $districtInfo = $provinceInfo->districts()->where('code', $request->customer['districts_code'])->first();
            $wardInfo = $districtInfo->wards()->where('code', $request->customer['wards_code'])->first();

            // Tạo thông tin địa chỉ khách hàng
            $customerAddress = Customer::create([
                'order_id' => $order->id,
                'first_name' => $request->customer['firstName'],
                'last_name' => $request->customer['lastName'],
                'email' => $request->customer['email'],
                'phone' => $request->customer['phone'],
                'provinces_code' => $provinceInfo['code'] ?? $request->customer['provinces_code'],
                'provinces_name' => $provinceInfo['name'],
                'districts_code' => $districtInfo['code'] ?? $request->customer['districts_code'],
                'districts_name' => $districtInfo['name'],
                'wards_code' => $wardInfo['code'] ?? $request->customer['wards_code'],
                'wards_name' => $wardInfo['name'] ,
                'address' => $request->customer['address'],
                'note' => $request->customer['note'] ?? null,
            ]);

            DB::commit();

            // Trả về kết quả thành công
            return response()->json([
                'message' => 'Đặt hàng thành công',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => $order->total,
                    'created_at' => $order->created_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
}