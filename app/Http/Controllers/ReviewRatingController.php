<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ReviewRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewRatingController extends Controller
{
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user')
            ->published()
            ->latest()
            ->paginate(6);

        return view('client.pages.product.reviews', [
            'product' => $product,
            'reviews' => $reviews
        ]);
    }

    public function loadMore(Product $product, Request $request)
    {
        $page = $request->query('page', 2);
        $sortBy = $request->query('sort', 'latest');

        $reviewsQuery = $product->reviews()->with('user')->published();

        // Áp dụng logic sắp xếp
        switch ($sortBy) {
            case 'oldest':
                $reviewsQuery->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $reviewsQuery->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'lowest':
                $reviewsQuery->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                break;
            default: // 'latest'
                $reviewsQuery->orderBy('created_at', 'desc');
                break;
        }

        $reviews = $reviewsQuery->paginate(6, ['*'], 'page', $page);

        if ($reviews->isEmpty()) {
            return '<div class="col-12 text-center py-4">No more reviews to load</div>';
        }

        return view('client.pages.product.review-list', [
            'reviews' => $reviews
        ])->render();
    }

    public function create(Order $order)
    {
        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        // Kiểm tra xem đơn hàng có thể được đánh giá không
        if (!$order->canBeReviewed()) {
            return redirect()->route('user.orders')
                ->with('error', 'Only completed orders can be reviewed');
        }

        // Lấy danh sách sản phẩm có thể đánh giá
        $reviewableProducts = $order->getReviewableProducts(Auth::id());

        if ($reviewableProducts->isEmpty()) {
            return redirect()->route('user.orders.detail', $order)
                ->with('info', 'You have already reviewed all products in this order');
        }

        return view('client.pages.account.reviews.create', [
            'order' => $order,
            'reviewableProducts' => $reviewableProducts,
            'breadcrumbItems' => [
                ['title' => 'Home', 'url' => route('home')],
                ['title' => 'My Account', 'url' => route('user.my.account')],
                ['title' => 'My Orders', 'url' => route('user.orders')],
                ['title' => 'Order #' . $order->order_code, 'url' => route('user.orders.detail', $order)],
                ['title' => 'Write Review', 'url' => null, 'active' => true]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10|max:1000'
        ]);

        $order = Order::findOrFail($validatedData['order_id']);

        // Kiểm tra quyền
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        // Kiểm tra xem đơn hàng có thể được đánh giá không
        if (!$order->canBeReviewed()) {
            return redirect()->route('user.orders')
                ->with('error', 'Only completed orders can be reviewed');
        }

        // Kiểm tra xem sản phẩm có trong đơn hàng không
        $productInOrder = $order->items()
            ->whereHas('product', function ($query) use ($validatedData) {
                $query->where('product_id', $validatedData['product_id']);
            })
            ->exists();

        if (!$productInOrder) {
            return redirect()->back()->with('error', 'This product is not in your order');
        }

        // Kiểm tra xem sản phẩm đã được đánh giá chưa
        $alreadyReviewed = ReviewRating::where('user_id', Auth::id())
            ->where('product_id', $validatedData['product_id'])
            ->where('order_id', $validatedData['order_id'])
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->back()->with('error', 'You have already reviewed this product');
        }

        // Tạo đánh giá mới
        $review = ReviewRating::create([
            'user_id' => Auth::id(),
            'product_id' => $validatedData['product_id'],
            'order_id' => $validatedData['order_id'],
            'rating' => $validatedData['rating'],
            'content' => $validatedData['content'],
            'status' => ReviewRating::STATUS_PUBLISHED
        ]);

        return redirect()->route('user.orders.detail', $order)
            ->with('success', 'Thank you for your review!');
    }
}
