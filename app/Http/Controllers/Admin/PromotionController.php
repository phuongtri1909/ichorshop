<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        ],[
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
        $data['status'] = $data['status'] ? Promotion::STATUS_ACTIVE : Promotion::STATUS_INACTIVE;
        
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
}
