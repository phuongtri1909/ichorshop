<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Tính phí vận chuyển
     */
    public function calculate(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'provinces_code' => 'required|string|exists:provinces,code',
            'districts_code' => 'required|string|exists:districts,code',
            'wards_code' => 'required|string|exists:wards,code',
            'product_weight_id' => 'required|exists:product_weights,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'provinces_code.required' => 'Tỉnh/Thành phố không được để trống',
            'districts_code.required' => 'Quận/Huyện không được để trống',
            'wards_code.required' => 'Phường/Xã không được để trống',
            'product_weight_id.required' => 'Trọng lượng sản phẩm không được để trống',
            'quantity.required' => 'Số lượng không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Tính phí vận chuyển sử dụng service
            $result = $this->shippingService->calculate(
                $request->provinces_code,
                $request->districts_code,
                $request->wards_code,
                $request->product_weight_id,
                $request->quantity
            );
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
}
