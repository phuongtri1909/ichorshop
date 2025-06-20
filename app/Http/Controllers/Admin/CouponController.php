<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Product;
use App\Mail\CouponMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * Hiển thị danh sách mã giảm giá
     */
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.pages.coupon.index', compact('coupons'));
    }

    public function loadProducts(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $limit = 15;

        $query = Product::with(['variants' => function ($query) {
            $query->where('status', ProductVariant::STATUS_ACTIVE);
        }])
            ->where('status', Product::STATUS_ACTIVE)
            ->where(function ($q) use ($search) {
                if (!empty($search)) {
                    $q->where('name', 'like', "%$search%");
                }
            })
            ->whereHas('variants', function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('status', ProductVariant::STATUS_ACTIVE)
                        ->where(function ($q) use ($search) {
                            $q->where('sku', 'like', "%$search%")
                                ->orWhere('color_name', 'like', "%$search%")
                                ->orWhere('size', 'like', "%$search%");
                        });
                } else {
                    $query->where('status', ProductVariant::STATUS_ACTIVE);
                }
            });

        $total = $query->count();
        $products = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $hasMore = ($page * $limit) < $total;

        $html = view('admin.pages.coupon.partials.product-list', [
            'products' => $products
        ])->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hasMore,
            'total' => $total
        ]);
    }

    /**
     * Tải danh sách người dùng theo phân trang
     */
    public function loadUsers(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $limit = 20;
        $selectedUserIds = $request->get('selected_user_ids', []);
        $excludeIds = $request->get('exclude_ids', []);
        $onlyAssigned = $request->boolean('only_assigned', false);

        // ID của mã giảm giá để kiểm tra người dùng đã được gán
        $couponId = $request->get('coupon_id');
        $assignedUserIds = [];

        if ($couponId) {
            // Lấy danh sách người dùng đã được gán mã giảm giá
            $coupon = Coupon::find($couponId);
            if ($coupon) {
                $assignedUserIds = $coupon->users()->pluck('users.id')->toArray();
            }
        }

        $query = User::where(function ($q) use ($search) {
            if (!empty($search)) {
                $q->where('full_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            }
        });

        // Nếu chỉ hiển thị người dùng đã gán
        if ($onlyAssigned && !empty($assignedUserIds)) {
            $query->whereIn('id', $assignedUserIds);
        } else if (empty($search)) {
            // Khi không có tìm kiếm, chỉ lấy người dùng đang hoạt động
            $query->where('active', User::ACTIVE_YES);
        }

        // Loại bỏ các ID đã được tải trước đó
        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        $total = $query->count();
        $users = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $hasMore = ($page * $limit) < $total;

        // Nếu trả về HTML fragment
        if ($request->get('format') === 'html') {
            $html = view('admin.pages.coupon.partials.user-list', [
                'users' => $users,
                'selectedUserIds' => $selectedUserIds,
                'assignedUserIds' => $assignedUserIds
            ])->render();

            return response()->json([
                'html' => $html,
                'hasMore' => $hasMore,
                'total' => $total
            ]);
        }

        // Trả về dạng JSON cho Select2
        return response()->json([
            'users' => $users->map(function ($user) use ($assignedUserIds) {
                return [
                    'id' => $user->id,
                    'text' => $user->full_name . ' (' . $user->email . ')',
                    'already_assigned' => in_array($user->id, $assignedUserIds),
                    'full_name' => $user->full_name,
                    'email' => $user->email
                ];
            }),
            'hasMore' => $hasMore,
            'total' => $total
        ]);
    }

    public function getInitialUsers(Request $request)
    {
        // Các ID người dùng đã được chọn (nếu có)
        $selectedUserIds = $request->get('selected_user_ids', []);

        // ID của mã giảm giá để kiểm tra người dùng đã được gán
        $couponId = $request->get('coupon_id');
        $onlyAssigned = $request->boolean('only_assigned', false);
        $assignedUserIds = [];

        if ($couponId) {
            // Lấy danh sách người dùng đã được gán mã giảm giá
            $coupon = Coupon::find($couponId);
            if ($coupon) {
                $assignedUserIds = $coupon->users()->pluck('users.id')->toArray();
            }
        }

        // Nếu chỉ hiển thị người dùng đã gán và có assignedUserIds
        if ($onlyAssigned && !empty($assignedUserIds)) {
            $users = User::whereIn('id', $assignedUserIds)
                ->latest()
                ->take(20)
                ->get();

            $total = count($assignedUserIds);
        } else {
            // Load 20 người dùng active đầu tiên
            $query = User::where('active', User::ACTIVE_YES);

            // Nếu có selectedUserIds, ưu tiên tải những người dùng này trước
            if (!empty($selectedUserIds)) {
                $priorityUsers = User::whereIn('id', $selectedUserIds)->get();

                $query = $query->whereNotIn('id', $selectedUserIds);
                $users = $query->latest()->take(20 - count($selectedUserIds))->get();

                $users = $priorityUsers->merge($users);
            } else {
                $users = $query->latest()->take(20)->get();
            }

            $total = User::where('active', User::ACTIVE_YES)->count();
        }

        $html = view('admin.pages.coupon.partials.user-list', [
            'users' => $users,
            'selectedUserIds' => $selectedUserIds,
            'assignedUserIds' => $assignedUserIds
        ])->render();

        return response()->json([
            'html' => $html,
            'total' => $total,
            'users' => $users->map(function ($user) use ($assignedUserIds) {
                return [
                    'id' => $user->id,
                    'text' => $user->full_name . ' (' . $user->email . ')',
                    'already_assigned' => in_array($user->id, $assignedUserIds),
                    'full_name' => $user->full_name,
                    'email' => $user->email
                ];
            })
        ]);
    }

    /**
     * Hiển thị form tạo mã giảm giá mới
     */
    public function create()
    {
        return view('admin.pages.coupon.create');
    }

    /**
     * Lưu mã giảm giá mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:coupons,code|max:20',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'product_variants' => 'nullable|array',
            'product_variants.*' => 'exists:product_variants,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Mã giảm giá là bắt buộc',
            'code.unique' => 'Mã giảm giá đã tồn tại',
            'code.max' => 'Mã giảm giá không được vượt quá 20 ký tự',
            'type.in' => 'Loại mã giảm giá không hợp lệ',
            'type.required' => 'Loại mã giảm giá là bắt buộc',
            'value.required' => 'Giá trị giảm giá là bắt buộc',
            'value.numeric' => 'Giá trị giảm giá phải là số',
            'value.min' => 'Giá trị giảm giá phải lớn hơn hoặc bằng 0',
            'min_order_amount.numeric' => 'Giá trị đơn hàng tối thiểu phải là số',
            'min_order_amount.min' => 'Giá trị đơn hàng tối thiểu phải lớn hơn hoặc bằng 0',
            'max_discount_amount.numeric' => 'Giá trị giảm giá tối đa phải là số',
            'max_discount_amount.min' => 'Giá trị giảm giá tối đa phải lớn hơn hoặc bằng 0',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ',
            'end_date.date' => 'Ngày kết thúc không hợp lệ',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên',
            'usage_limit.min' => 'Giới hạn sử dụng phải lớn hơn hoặc bằng 0',
            'usage_limit_per_user.integer' => 'Giới hạn sử dụng cho mỗi người dùng phải là số nguyên',
            'usage_limit_per_user.min' => 'Giới hạn sử dụng cho mỗi người dùng phải lớn hơn hoặc bằng 0',
            'description.string' => 'Mô tả phải là chuỗi',
            'product_variants.array' => 'Biến thể sản phẩm phải là mảng',
            'product_variants.*.exists' => 'Biến thể sản phẩm không tồn tại',
            'users.array' => 'Người dùng phải là mảng',
            'users.*.exists' => 'Người dùng không tồn tại',
            'is_active.boolean' => 'Trạng thái kích hoạt phải là đúng hoặc sai'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Nếu là giảm giá theo phần trăm, kiểm tra giá trị tối đa là 100%
        if ($request->type === 'percentage' && $request->value > 100) {
            return response()->json([
                'errors' => ['value' => ['Giá trị giảm giá theo phần trăm không thể vượt quá 100%']]
            ], 422);
        }

        // Tạo mã giảm giá mới
        $coupon = Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit ?? 0,
            'usage_limit_per_user' => $request->usage_limit_per_user ?? 0,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        // Liên kết với các biến thể sản phẩm
        if ($request->has('product_variants')) {
            $coupon->productVariants()->sync($request->product_variants);
        }

        // Liên kết với các người dùng
        if ($request->has('users')) {
            $coupon->users()->sync($request->users);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mã giảm giá đã được tạo thành công',
            'redirect' => route('admin.coupons.index')
        ]);
    }

    /**
     * Hiển thị form chỉnh sửa mã giảm giá
     */
    public function edit(Coupon $coupon)
    {
        $coupon->load('productVariants', 'users');

        $selectedVariantIds = $coupon->productVariants ? $coupon->productVariants->pluck('id')->toArray() : [];
        $selectedUserIds = $coupon->users ? $coupon->users->pluck('id')->toArray() : [];

        $selectedUsers = User::whereIn('id', $selectedUserIds)->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->full_name . ' (' . $user->email . ')',
                'email' => $user->email,
                'full_name' => $user->full_name,
            ];
        })->toArray();

        $selectedProducts = [];
        if (!empty($selectedVariantIds)) {
            $variantProductIds = ProductVariant::whereIn('id', $selectedVariantIds)
                ->distinct()
                ->pluck('product_id');

            $selectedProducts = Product::with(['variants' => function ($query) use ($selectedVariantIds) {
                $query->whereIn('id', $selectedVariantIds)
                    ->where('status', ProductVariant::STATUS_ACTIVE);
            }])->whereIn('id', $variantProductIds)
                ->where('status', Product::STATUS_ACTIVE)
                ->get();
        }

        return view('admin.pages.coupon.edit', compact(
            'coupon',
            'selectedProducts',
            'selectedUsers',
            'selectedVariantIds',
            'selectedUserIds'
        ));
    }

    /**
     * Cập nhật thông tin mã giảm giá
     */
    public function update(Request $request, Coupon $coupon)
    {

        $validator = Validator::make($request->all(), [
            'code' => 'required|max:20|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'product_variants' => 'nullable|array',
            'product_variants.*' => 'exists:product_variants,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Mã giảm giá là bắt buộc',
            'code.unique' => 'Mã giảm giá đã tồn tại',
            'code.max' => 'Mã giảm giá không được vượt quá 20 ký tự',
            'type.in' => 'Loại mã giảm giá không hợp lệ',
            'type.required' => 'Loại mã giảm giá là bắt buộc',
            'value.required' => 'Giá trị giảm giá là bắt buộc',
            'value.numeric' => 'Giá trị giảm giá phải là số',
            'value.min' => 'Giá trị giảm giá phải lớn hơn hoặc bằng 0',
            'min_order_amount.numeric' => 'Giá trị đơn hàng tối thiểu phải là số',
            'min_order_amount.min' => 'Giá trị đơn hàng tối thiểu phải lớn hơn hoặc bằng 0',
            'max_discount_amount.numeric' => 'Giá trị giảm giá tối đa phải là số',
            'max_discount_amount.min' => 'Giá trị giảm giá tối đa phải lớn hơn hoặc bằng 0',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ',
            'end_date.date' => 'Ngày kết thúc không hợp lệ',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên',
            'usage_limit.min' => 'Giới hạn sử dụng phải lớn hơn hoặc bằng 0',
            'usage_limit_per_user.integer' => 'Giới hạn sử dụng cho mỗi người dùng phải là số nguyên',
            'usage_limit_per_user.min' => 'Giới hạn sử dụng cho mỗi người dùng phải lớn hơn hoặc bằng 0',
            'description.string' => 'Mô tả phải là chuỗi',
            'product_variants.array' => 'Biến thể sản phẩm phải là mảng',
            'product_variants.*.exists' => 'Biến thể sản phẩm không tồn tại',
            'users.array' => 'Người dùng phải là mảng',
            'users.*.exists' => 'Người dùng không tồn tại',
            'is_active.boolean' => 'Trạng thái kích hoạt phải là đúng hoặc sai'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Nếu là giảm giá theo phần trăm, kiểm tra giá trị tối đa là 100%
        if ($request->type === 'percentage' && $request->value > 100) {
            return response()->json([
                'errors' => ['value' => ['Giá trị giảm giá theo phần trăm không thể vượt quá 100%']]
            ], 422);
        }

        // Cập nhật thông tin mã giảm giá
        $coupon->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit ?? 0,
            'usage_limit_per_user' => $request->usage_limit_per_user ?? 0,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        // Cập nhật liên kết với các biến thể sản phẩm
        $coupon->productVariants()->sync($request->product_variants ?? []);

        // Cập nhật liên kết với các người dùng
        $coupon->users()->sync($request->users ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Mã giảm giá đã được cập nhật thành công',
            'redirect' => route('admin.coupons.index')
        ]);
    }

    /**
     * Xóa mã giảm giá
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Mã giảm giá đã được xóa thành công.');
    }

    /**
     * Tạo mã giảm giá ngẫu nhiên
     */
    public function generateCode()
    {
        $code = strtoupper(Str::random(8));

        // Đảm bảo code là duy nhất
        while (Coupon::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }

        return response()->json(['code' => $code]);
    }

    /**
     * Lấy danh sách biến thể của sản phẩm
     */
    public function getProductVariants($productId)
    {
        $variants = ProductVariant::where('product_id', $productId)
            ->where('status', ProductVariant::STATUS_ACTIVE)
            ->get();

        return response()->json(['variants' => $variants]);
    }

    /**
     * Hiển thị form để gửi mã giảm giá cho người dùng
     */
    public function showSendForm(Coupon $coupon)
    {
        $coupon->load('users');

        $selectedUserIds = $coupon->users ? $coupon->users->pluck('id')->toArray() : [];

        $selectedUsers = User::whereIn('id', $selectedUserIds)->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->full_name . ' (' . $user->email . ')',
                'email' => $user->email,
                'full_name' => $user->full_name,
            ];
        })->toArray();

        return view('admin.pages.coupon.send', compact(
            'coupon',
            'selectedUsers',
            'selectedUserIds'
        ));
    }

    /**
     * Gửi mã giảm giá cho người dùng qua email
     */
    public function sendCoupon(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'assign' => 'boolean'
        ], [
            'users.required' => 'Bạn phải chọn ít nhất một người dùng để gửi mã giảm giá.',
            'users.array' => 'Danh sách người dùng phải là mảng.',
            'users.*.exists' => 'Một hoặc nhiều người dùng không tồn tại.',
            'assign.boolean' => 'Trường assign phải là đúng hoặc sai.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userIds = $request->users;

        // Lấy danh sách người dùng đã được gán với mã giảm giá
        $assignedUserIds = $coupon->users()->pluck('users.id')->toArray();

        // Lọc ra những người dùng chưa được gán trước đó
        $newUserIds = array_diff($userIds, $assignedUserIds);
        $users = User::whereIn('id', $newUserIds)->get();

         // Nếu chọn gán mã giảm giá, thực hiện gán
        if ($request->has('assign') && $request->assign) {
            $coupon->users()->syncWithoutDetaching($newUserIds);
        }

        $sent = 0;
        $failed = 0;
        $alreadyAssigned = count($userIds) - count($newUserIds);

        // Gửi email cho những người dùng chưa được gán
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new CouponMail($coupon, $user));
                $sent++;
            } catch (\Exception $e) {
                \Log::error('Failed to send coupon email', [
                    'user' => $user->id,
                    'coupon' => $coupon->id,
                    'error' => $e->getMessage()
                ]);
                $failed++;
            }
        }

        // Tạo thông báo phù hợp
        $message = '';
        $alertType = 'success';

        if ($sent > 0) {
            $message .= "Đã gửi mã giảm giá cho {$sent} người dùng mới. ";
        }

        if ($alreadyAssigned > 0) {
            $message .= "Có {$alreadyAssigned} người dùng đã được gán trước đó nên không gửi lại email. ";
        }

        if ($failed > 0) {
            $message .= "{$failed} người dùng không gửi được.";
            $alertType = 'warning';
        }

        if ($sent === 0 && $alreadyAssigned > 0 && $failed === 0) {
            $message = "Tất cả người dùng đã được gán mã giảm giá trước đó nên không gửi email.";
            $alertType = 'info';
        }

        return redirect()->route('admin.coupons.index')
            ->with($alertType, $message);
    }
}
