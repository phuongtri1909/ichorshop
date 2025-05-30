<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Lấy danh sách tất cả sản phẩm cho bộ lọc
        $products = Product::orderBy('name')->get();
        
        // Xây dựng query với các filter
        $reviewsQuery = Review::with('product')->orderBy('created_at', 'desc');
        
        // Filter theo sản phẩm
        if ($request->has('product_id') && !empty($request->product_id)) {
            $reviewsQuery->where('product_id', $request->product_id);
        }
        
        // Filter theo tên người đánh giá
        if ($request->has('user_name') && !empty($request->user_name)) {
            $reviewsQuery->where('user_name', 'like', '%' . $request->user_name . '%');
        }
        
        // Filter theo khoảng thời gian
        if ($request->has('date_from') && !empty($request->date_from)) {
            $reviewsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $reviewsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Thực thi query và phân trang kết quả
        $reviews = $reviewsQuery->paginate(10);
        
        return view('admin.pages.reviews.index', compact('reviews', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.reviews.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_name' => 'required|string|max:255',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'avatar' => 'nullable|image|max:1024',
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm',
            'product_id.exists' => 'Sản phẩm không tồn tại',
            'user_name.required' => 'Tên người đánh giá không được để trống',
            'rating.required' => 'Đánh giá không được để trống',
            'rating.min' => 'Đánh giá tối thiểu là 1 sao',
            'rating.max' => 'Đánh giá tối đa là 5 sao',
            'comment.required' => 'Nội dung đánh giá không được để trống',
            'comment.max' => 'Nội dung đánh giá không được vượt quá 1000 ký tự',
            'avatar.image' => 'File phải là hình ảnh',
            'avatar.max' => 'Kích thước hình ảnh không được vượt quá 1MB',
        ]);
        
        // Xử lý và lưu avatar nếu có
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = ImageHelper::optimizeAndSave($request->file('avatar'), 'avatars', 100);
        }

        Review::create($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đánh giá đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return redirect()->route('admin.reviews.edit', $review);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.reviews.edit', compact('review', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_name' => 'required|string|max:255',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'avatar' => 'nullable|image|max:1024',
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm',
            'product_id.exists' => 'Sản phẩm không tồn tại',
            'user_name.required' => 'Tên người đánh giá không được để trống',
            'rating.required' => 'Đánh giá không được để trống',
            'rating.min' => 'Đánh giá tối thiểu là 1 sao',
            'rating.max' => 'Đánh giá tối đa là 5 sao',
            'comment.required' => 'Nội dung đánh giá không được để trống',
            'comment.max' => 'Nội dung đánh giá không được vượt quá 1000 ký tự',
            'avatar.image' => 'File phải là hình ảnh',
            'avatar.max' => 'Kích thước hình ảnh không được vượt quá 1MB',
        ]);
        
        // Xử lý và lưu avatar mới nếu có
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($review->avatar) {
                ImageHelper::delete($review->avatar);
            }
            $validated['avatar'] = ImageHelper::optimizeAndSave($request->file('avatar'), 'avatars', 100);
        }

        $review->update($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đánh giá đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        // Xóa avatar nếu có
        if ($review->avatar) {
            ImageHelper::delete($review->avatar);
        }
        
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đánh giá đã được xóa thành công.');
    }
}