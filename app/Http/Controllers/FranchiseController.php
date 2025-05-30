<?php

namespace App\Http\Controllers;

use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FranchiseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Franchise::query();

        // Filter theo tên
        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter theo mã gói
        if ($request->has('code') && !empty($request->code)) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        // Sắp xếp theo thứ tự hoặc mới nhất
        $sortField = $request->input('sort_by', 'sort_order');
        $sortDirection = $request->input('sort_direction', 'asc');

        if ($sortField === 'created_at') {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('sort_order', 'asc');
        }

        $franchises = $query->paginate(10);

        return view('admin.pages.franchise.index', compact('franchises'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.franchise.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_package' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:franchises',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'required|max:500000',
            'detail_items' => 'nullable|array',
            'detail_items.*' => 'required|string',
        ],
        [
            'name.required' => 'Tên gói nhượng quyền là bắt buộc.',
            'name.max' => 'Tên gói nhượng quyền không được vượt quá :max ký tự.',
            'name_package.required' => 'Tên gói là bắt buộc.',
            'name_package.max' => 'Tên gói không được vượt quá :max ký tự.',
            'code.unique' => 'Mã gói đã tồn tại.',
            'code.max' => 'Mã gói không được vượt quá :max ký tự.',
            'sort_order.integer' => 'Thứ tự phải là một số nguyên.',
            'sort_order.min' => 'Thứ tự không được nhỏ hơn 0.',
            'description.required' => 'Mô tả là bắt buộc.',
            'description.max' => 'Mô tả không được vượt quá :max ký tự.',
            'detail_items.array' => 'Chi tiết gói phải là một mảng.',
            'detail_items.*.required' => 'Chi tiết gói không được để trống.',
            'detail_items.*.string' => 'Chi tiết gói phải là một chuỗi.',
        ]);

        // Xử lý chi tiết gói - lưu dạng mảng đơn giản
        $details = [];
        if ($request->has('detail_items')) {
            foreach ($request->detail_items as $item) {
                if (!empty($item)) {
                    $details[] = $item;
                }
            }
        }

        // Tạo slug từ tên
        $slug = Str::slug($request->name);

        // Kiểm tra và thêm số vào slug nếu trùng
        $count = 1;
        $originalSlug = $slug;
        while (Franchise::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Xử lý mã gói - nếu không có thì tạo từ tên hiển thị
        $code = $request->code;
        if (empty($code)) {
            // Tạo mã từ tên hiển thị (name_package), chuyển thành slug rồi thành chữ hoa
            $packageSlug = Str::slug($request->name_package);
            $code = strtoupper(str_replace('-', '_', $packageSlug));

            // Kiểm tra và thêm số nếu mã trùng
            $codeCount = 1;
            $originalCode = $code;
            while (Franchise::where('code', $code)->exists()) {
                $code = $originalCode . '_' . $codeCount++;
            }
        }

        Franchise::create([
            'name' => $request->name,
            'name_package' => $request->name_package,
            'slug' => $slug,
            'code' => $code,
            'sort_order' => $request->sort_order ?? 0,
            'description' => $request->description,
            'details' => json_encode($details),
        ]);

        return redirect()->route('admin.franchise.index')
            ->with('success', 'Gói nhượng quyền đã được tạo thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Franchise $franchise)
    {
        // Chuyển đổi details từ JSON string sang array đơn giản
        $franchise->detailItems = json_decode($franchise->details, true) ?? [];

        return view('admin.pages.franchise.edit', compact('franchise'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Franchise $franchise)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_package' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:franchises,code,' . $franchise->id,
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'required|max:500000',
            'detail_items' => 'nullable|array',
            'detail_items.*' => 'required|string',
        ],
        [
            'name.required' => 'Tên gói nhượng quyền là bắt buộc.',
            'name.max' => 'Tên gói nhượng quyền không được vượt quá :max ký tự.',
            'name_package.required' => 'Tên gói là bắt buộc.',
            'name_package.max' => 'Tên gói không được vượt quá :max ký tự.',
            'code.unique' => 'Mã gói đã tồn tại.',
            'code.max' => 'Mã gói không được vượt quá :max ký tự.',
            'sort_order.integer' => 'Thứ tự phải là một số nguyên.',
            'sort_order.min' => 'Thứ tự không được nhỏ hơn 0.',
            'description.required' => 'Mô tả là bắt buộc.',
            'description.max' => 'Mô tả không được vượt quá :max ký tự.',
            'detail_items.array' => 'Chi tiết gói phải là một mảng.',
            'detail_items.*.required' => 'Chi tiết gói không được để trống.',
            'detail_items.*.string' => 'Chi tiết gói phải là một chuỗi.',
        ]);

        // Xử lý chi tiết gói - lưu dạng mảng đơn giản
        $details = [];
        if ($request->has('detail_items')) {
            foreach ($request->detail_items as $item) {
                if (!empty($item)) {
                    $details[] = $item;
                }
            }
        }

        // Cập nhật slug nếu tên thay đổi
        if ($franchise->name !== $request->name) {
            $slug = Str::slug($request->name);

            // Kiểm tra và thêm số vào slug nếu trùng
            $count = 1;
            $originalSlug = $slug;
            while (Franchise::where('slug', $slug)->where('id', '!=', $franchise->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $franchise->slug = $slug;
        }

        // Xử lý mã gói - nếu không có thì tạo từ tên hiển thị hiện tại
        if (empty($request->code)) {
            // Tạo mã từ tên hiển thị (name_package), chuyển thành slug rồi thành chữ hoa
            $packageSlug = Str::slug($request->name_package);
            $code = strtoupper(str_replace('-', '_', $packageSlug));

            // Kiểm tra và thêm số nếu mã trùng
            $codeCount = 1;
            $originalCode = $code;
            while (Franchise::where('code', $code)->where('id', '!=', $franchise->id)->exists()) {
                $code = $originalCode . '_' . $codeCount++;
            }

            $franchise->code = $code;
        } else {
            $franchise->code = $request->code;
        }

        $franchise->name = $request->name;
        $franchise->name_package = $request->name_package;
        $franchise->sort_order = $request->sort_order ?? 0;
        $franchise->description = $request->description;
        $franchise->details = json_encode($details);
        $franchise->save();

        return redirect()->route('admin.franchise.index')
            ->with('success', 'Gói nhượng quyền đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Franchise $franchise)
    {
        $franchise->delete();

        return redirect()->route('admin.franchise.index')
            ->with('success', 'Gói nhượng quyền đã được xóa thành công!');
    }
}
