<?php

namespace App\Http\Controllers\Api;
use App\Models\Province;

use App\Models\Franchise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FranchiseResource;
use App\Models\FranchiseContact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FranchiseController extends Controller
{
    /**
     * Lấy danh sách tất cả gói nhượng quyền với phân trang và filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Franchise::query();

            // Sắp xếp theo thứ tự hiển thị hoặc ngày tạo
            $sortField = $request->input('sort_by', 'sort_order');
            $sortDirection = $request->input('sort_direction', 'asc');
            $allowedSortFields = ['name', 'sort_order', 'created_at'];

            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('sort_order', 'asc');
            }

            // Phân trang
            $perPage = min(max((int)$request->input('per_page', 10), 1), 50); // Giới hạn 1-50 items/page
            $franchises = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'current_page' => $franchises->currentPage(),
                    'data' => FranchiseResource::collection($franchises),
                    'first_page_url' => $franchises->url(1),
                    'from' => $franchises->firstItem(),
                    'last_page' => $franchises->lastPage(),
                    'last_page_url' => $franchises->url($franchises->lastPage()),
                    'links' => $franchises->linkCollection()->toArray(),
                    'next_page_url' => $franchises->nextPageUrl(),
                    'path' => $franchises->path(),
                    'per_page' => $franchises->perPage(),
                    'prev_page_url' => $franchises->previousPageUrl(),
                    'to' => $franchises->lastItem(),
                    'total' => $franchises->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin chi tiết của một gói nhượng quyền
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $franchise = Franchise::where('slug', $slug)
                ->firstOrFail();

            // Lấy các gói nhượng quyền khác để so sánh
            $otherFranchises = Franchise::where('id', '!=', $franchise->id)
                ->orderBy('sort_order', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'franchise' => new FranchiseResource($franchise),
                    'other_franchises' => FranchiseResource::collection($otherFranchises)
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói nhượng quyền'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function FranchiseContact(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'franchise_code' => 'required|exists:franchises,code',

            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'provinces_code' => 'required|string|exists:provinces,code',
            'districts_code' => 'required|string|exists:districts,code',
            'wards_code' => 'required|string|exists:wards,code',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000',
        ],
        [
            'franchise_code.required' => 'Mã nhượng quyền không được để trống',
            'franchise_code.exists' => 'Mã nhượng quyền không tồn tại',
            'firstName.required' => 'Họ tên không được để trống',
            'email.required' => 'Email không được để trống',
            'phone.required' => 'Số điện thoại không được để trống',
            'provinces_code.required' => 'Tỉnh/Thành phố không được để trống',
            'districts_code.required' => 'Quận/Huyện không được để trống',
            'wards_code.required' => 'Phường/Xã không được để trống',
            'address.required' => 'Địa chỉ không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {

            $provinceInfo = Province::where('code', $request->provinces_code)->first();
            $districtInfo = $provinceInfo->districts()->where('code', $request->districts_code)->first();
            $wardInfo = $districtInfo->wards()->where('code', $request->wards_code)->first();

            // Tạo thông tin địa chỉ khách hàng
            $franchiseContact = FranchiseContact::create([
                'franchise_code' => $request->franchise_code,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'phone' => $request->phone,
                'provinces_code' => $provinceInfo['code'] ?? $request->provinces_code,
                'provinces_name' => $provinceInfo['name'],
                'districts_code' => $districtInfo['code'] ?? $request->districts_code,
                'districts_name' => $districtInfo['name'],
                'wards_code' => $wardInfo['code'] ?? $request->wards_code,
                'wards_name' => $wardInfo['name'] ,
                'address' => $request->address,
                'note' => $request->note ?? null,
            ]);

            // Trả về kết quả thành công
            return response()->json([
                'success' => true,
                'message' => 'Thông tin liên hệ đã được gửi thành công',
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra khi gửi thông tin liên hệ: ' . $e->getMessage()], 500);
        }
    }
}