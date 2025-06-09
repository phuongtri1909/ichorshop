<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use App\Models\ProductVariantPromotion;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::latest()->paginate(20);
        return view('admin.pages.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.pages.promotions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
        ], [
            'name.required' => 'Tên khuyến mãi là bắt buộc',
            'name.string' => 'Tên khuyến mãi phải là chuỗi',
            'name.max' => 'Tên khuyến mãi không được vượt quá 255 ký tự',
            'description.string' => 'Mô tả khuyến mãi phải là chuỗi',
            'type.required' => 'Loại khuyến mãi là bắt buộc',
            'type.in' => 'Loại khuyến mãi không hợp lệ',
            'value.required' => 'Giá trị khuyến mãi là bắt buộc',
            'value.numeric' => 'Giá trị khuyến mãi phải là số',
            'value.min' => 'Giá trị khuyến mãi phải lớn hơn hoặc bằng 0',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc',
            'start_date.date' => 'Ngày bắt đầu phải là một ngày giờ hợp lệ',
            'start_date.after_or_equal' => 'Ngày bắt đầu phải là ngày giờ hiện tại hoặc sau đó',
            'end_date.required' => 'Ngày kết thúc là bắt buộc',
            'end_date.date' => 'Ngày kết thúc phải là một ngày giờ hợp lệ',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'min_order_amount.numeric' => 'Số tiền tối thiểu phải là số',
            'min_order_amount.min' => 'Số tiền tối thiểu phải lớn hơn hoặc bằng 0',
            'max_discount_amount.min' => 'Số tiền giảm tối đa phải lớn hơn hoặc bằng 0',
            'max_discount_amount.numeric' => 'Số tiền giảm tối đa phải là số',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên',
            'usage_limit.min' => 'Giới hạn sử dụng phải lớn hơn hoặc bằng 1',
        ]);

        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Phần trăm giảm không được vượt quá 100%']);
        }

        $data = $request->all();
        $data['start_date'] = Carbon::parse($request->start_date);
        $data['end_date'] = Carbon::parse($request->end_date);
        $data['status'] = $request->has('status') ? Promotion::STATUS_ACTIVE : Promotion::STATUS_INACTIVE;

        Promotion::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Khuyến mãi đã được tạo thành công',
            'redirect' => route('admin.promotions.index')
        ]);
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.pages.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
        ];

        // Chỉ validate start_date >= now nếu có thay đổi
        $currentStartDate = $promotion->start_date->format('Y-m-d\TH:i');
        if ($request->start_date !== $currentStartDate) {
            $rules['start_date'] .= '|after_or_equal:now';
        }

        $request->validate($rules, [
            'name.required' => 'Tên khuyến mãi là bắt buộc',
            'name.string' => 'Tên khuyến mãi phải là chuỗi',
            'name.max' => 'Tên khuyến mãi không được vượt quá 255 ký tự',
            'description.string' => 'Mô tả khuyến mãi phải là chuỗi',
            'type.required' => 'Loại khuyến mãi là bắt buộc',
            'type.in' => 'Loại khuyến mãi không hợp lệ',
            'value.required' => 'Giá trị khuyến mãi là bắt buộc',
            'value.numeric' => 'Giá trị khuyến mãi phải là số',
            'value.min' => 'Giá trị khuyến mãi phải lớn hơn hoặc bằng 0',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc',
            'start_date.date' => 'Ngày bắt đầu phải là một ngày hợp lệ',
            'start_date.after_or_equal' => 'Ngày bắt đầu mới phải từ thời điểm hiện tại trở đi',
            'end_date.required' => 'Ngày kết thúc là bắt buộc',
            'end_date.date' => 'Ngày kết thúc phải là một ngày hợp lệ',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'min_order_amount.numeric' => 'Số tiền tối thiểu phải là số',
            'min_order_amount.min' => 'Số tiền tối thiểu phải lớn hơn hoặc bằng 0',
            'max_discount_amount.min' => 'Số tiền giảm tối đa phải lớn hơn hoặc bằng 0',
            'max_discount_amount.numeric' => 'Số tiền giảm tối đa phải là số',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên',
            'usage_limit.min' => 'Giới hạn sử dụng phải lớn hơn hoặc bằng 1',
        ]);

        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Phần trăm giảm không được vượt quá 100%']);
        }

        $data = $request->all();
        $data['start_date'] = Carbon::parse($request->start_date);
        $data['end_date'] = Carbon::parse($request->end_date);
        $data['status'] = $request->has('status') ? Promotion::STATUS_ACTIVE : Promotion::STATUS_INACTIVE;

        $promotion->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Khuyến mãi đã được cập nhật thành công',
            'redirect' => route('admin.promotions.index')
        ]);
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Khuyến mãi đã được xóa thành công'
        ]);
    }

    public function variants(Promotion $promotion)
    {
        // Lấy tất cả các biến thể đang áp dụng khuyến mãi này
        $appliedVariants = ProductVariantPromotion::with(['productVariant.product'])
            ->where('promotion_id', $promotion->id)
            ->get()
            ->groupBy('productVariant.product_id');

        // Lấy tất cả sản phẩm có thể áp dụng khuyến mãi (chưa được áp dụng)
        $availableProducts = Product::active()->get();

        return view('admin.pages.promotions.variants', compact('promotion', 'appliedVariants', 'availableProducts'));
    }

    public function applyToVariants(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'variant_ids' => 'required_without:product_id|array',
            'variant_ids.*' => 'exists:product_variants,id',
        ]);

        $appliedCount = 0;
        $alreadyAppliedToCurrentCount = 0;
        $alreadyAppliedToOtherCount = 0;
        $appliedVariantNames = [];
        $alreadyAppliedToOtherNames = [];

        if (isset($validated['product_id'])) {
            // Áp dụng cho tất cả biến thể của sản phẩm
            $variants = ProductVariant::where('product_id', $validated['product_id'])
                ->where('status', ProductVariant::STATUS_ACTIVE)
                ->get();
        } else {
            // Áp dụng cho các biến thể đã chọn
            $variants = ProductVariant::whereIn('id', $validated['variant_ids'])
                ->where('status', ProductVariant::STATUS_ACTIVE)
                ->get();
        }

        foreach ($variants as $variant) {
            // Kiểm tra xem biến thể đã có khuyến mãi khác chưa
            $existingPromotion = ProductVariantPromotion::where('product_variant_id', $variant->id)
                ->first();

            // Nếu chưa có khuyến mãi nào hoặc đã áp dụng khuyến mãi hiện tại
            if (!$existingPromotion || $existingPromotion->promotion_id == $promotion->id) {
                if ($existingPromotion && $existingPromotion->promotion_id == $promotion->id) {
                    $alreadyAppliedToCurrentCount++;
                } else {
                    ProductVariantPromotion::firstOrCreate([
                        'product_variant_id' => $variant->id,
                        'promotion_id' => $promotion->id,
                    ]);
                    $appliedCount++;
                }
            } else {
                $alreadyAppliedToOtherCount++;
            }
        }

        // Xác định thông báo chính dựa trên kết quả
        if ($appliedCount > 0) {
            return redirect()->route('admin.promotions.variants', $promotion)
                ->with('success', "Đã áp dụng khuyến mãi thành công cho {$appliedCount} biến thể.");
        } elseif ($alreadyAppliedToCurrentCount > 0 && $alreadyAppliedToOtherCount == 0) {
            return redirect()->route('admin.promotions.variants', $promotion)
                ->with('info', "{$alreadyAppliedToCurrentCount} biến thể đã được áp dụng khuyến mãi này trước đó.");
        } elseif ($alreadyAppliedToOtherCount > 0) {
            return redirect()->route('admin.promotions.variants', $promotion)
                ->with('warning', "{$alreadyAppliedToOtherCount} biến thể không thể áp dụng vì đã có khuyến mãi khác.");
        } else {
            return redirect()->route('admin.promotions.variants', $promotion)
                ->with('error', 'Không có biến thể nào được xử lý.');
        }
    }

    public function removeVariant(ProductVariantPromotion $variantPromotion)
    {
        $promotionName = $variantPromotion->promotion->name;
        
        // Thông tin biến thể và sản phẩm để hiển thị thông báo
        $variant = $variantPromotion->productVariant;
        $variantInfo = $variant ? "{$variant->color_name} ({$variant->size})" : "biến thể không xác định";
        $productName = $variant && $variant->product ? $variant->product->name : "sản phẩm không xác định";
        
        // Xóa khuyến mãi
        $variantPromotion->delete();
        
        return redirect()->back()->with('success', "Đã xóa {$variantInfo} của {$productName} khỏi khuyến mãi '{$promotionName}'");
    }

    public function removeProductVariants($promotionId, $productId)
    {
        $promotion = Promotion::findOrFail($promotionId);
        
        // Đếm số biến thể bị ảnh hưởng
        $count = ProductVariantPromotion::where('promotion_id', $promotionId)
            ->whereHas('productVariant', function($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->orWhere(function($query) use ($promotionId, $productId) {
                // Tìm các bản ghi mà productVariant đã bị xóa mềm
                $query->where('promotion_id', $promotionId)
                    ->whereHas('productVariant', function($q) use ($productId) {
                        $q->withTrashed()->where('product_id', $productId);
                    });
            })
            ->count();
        
        // Xóa tất cả biến thể của sản phẩm khỏi khuyến mãi
        ProductVariantPromotion::where('promotion_id', $promotionId)
            ->whereHas('productVariant', function($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->orWhere(function($query) use ($promotionId, $productId) {
                // Xóa cả các bản ghi mà productVariant đã bị xóa mềm
                $query->where('promotion_id', $promotionId)
                    ->whereHas('productVariant', function($q) use ($productId) {
                        $q->withTrashed()->where('product_id', $productId);
                    });
            })
            ->delete();
        
        // Lấy tên sản phẩm
        $product = Product::withTrashed()->find($productId);
        $productName = $product ? $product->name : "sản phẩm không xác định";
        
        return redirect()->back()->with('success', "Đã xóa {$count} biến thể của {$productName} khỏi khuyến mãi '{$promotion->name}'");
    }

    public function getProductVariants(Request $request)
    {
        $productId = $request->input('product_id');
        $promotionId = $request->input('promotion_id');

        // Lấy tất cả biến thể của sản phẩm
        $variants = ProductVariant::where('product_id', $productId)
            ->where('status', ProductVariant::STATUS_ACTIVE)
            ->get();

        // Kiểm tra xem biến thể nào đã được áp dụng khuyến mãi
        foreach ($variants as $variant) {
            // Kiểm tra nếu biến thể đã được áp dụng khuyến mãi hiện tại
            $variant->applied_to_current = ProductVariantPromotion::where('product_variant_id', $variant->id)
                ->where('promotion_id', $promotionId)
                ->exists();

            // Kiểm tra nếu biến thể được áp dụng khuyến mãi khác
            $otherPromotion = ProductVariantPromotion::where('product_variant_id', $variant->id)
                ->where('promotion_id', '!=', $promotionId)
                ->first();

            if ($otherPromotion) {
                $variant->other_promotion = Promotion::find($otherPromotion->promotion_id);
            } else {
                $variant->other_promotion = null;
            }
        }

        return response()->json([
            'success' => true,
            'variants' => $variants
        ]);
    }
}
