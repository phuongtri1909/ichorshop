<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ReviewRating;
use App\Models\User;
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
        $reviewsQuery = ReviewRating::with(['product', 'user', 'order'])->orderBy('created_at', 'desc');
        
        // Filter theo sản phẩm
        if ($request->has('product_id') && !empty($request->product_id)) {
            $reviewsQuery->where('product_id', $request->product_id);
        }
        
        // Filter theo tên người đánh giá
        if ($request->has('full_name') && !empty($request->full_name)) {
            $keyword = $request->full_name;
            $reviewsQuery->whereHas('user', function($query) use ($keyword) {
                $query->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('full_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }
        
        // Filter theo khoảng thời gian
        if ($request->has('date_from') && !empty($request->date_from)) {
            $reviewsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $reviewsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter theo trạng thái
        if ($request->has('status') && !empty($request->status)) {
            $reviewsQuery->where('status', $request->status);
        }
        
        // Thực thi query và phân trang kết quả
        $reviews = $reviewsQuery->paginate(10);
        
        return view('admin.pages.reviews.index', compact('reviews', 'products'));
    }

    /**
     * Update the status of the specified review.
     */
    public function updateStatus(Request $request, ReviewRating $review)
    {
        $request->validate([
            'status' => 'required|in:published,pending,rejected'
        ]);

        $review->status = $request->status;
        $review->save();

        return redirect()->back()->with('success', 'Trạng thái đánh giá đã được cập nhật thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReviewRating $review)
    {
        $products = Product::orderBy('name')->get();
        $review->load(['user', 'product', 'order']);
        return view('admin.pages.reviews.edit', compact('review', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReviewRating $review)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|numeric|min:1|max:5',
            'content' => 'required|string|max:1000',
            'status' => 'required|in:published,pending,rejected',
        ]);

        $review->update($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đánh giá đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReviewRating $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đánh giá đã được xóa thành công.');
    }
}