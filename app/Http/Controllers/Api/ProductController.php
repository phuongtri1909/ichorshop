<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

class ProductController extends Controller
{
    /**
     * Lấy danh sách tất cả sản phẩm với phân trang và filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category'])
                ->where('is_active', true)
                ->select('id', 'category_id', 'name', 'slug', 'description', 'highlight', 'image', 'is_featured');

            // Filter theo danh mục
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter theo danh mục slug
            if ($request->has('category_slug')) {
                $category = Category::where('slug', $request->category_slug)->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }

            // Filter theo featured
            if ($request->has('featured')) {
                $query->where('is_featured', filter_var($request->featured, FILTER_VALIDATE_BOOLEAN));
            }

            // Search theo tên
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Sắp xếp
            $sortField = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $allowedSortFields = ['name', 'created_at'];

            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }

            // Phân trang
            $perPage = min(max((int)$request->input('per_page', 12), 1), 50); // Giới hạn 1-50 items/page
            $products = $query->paginate($perPage);

            // Thêm thông tin giá và đánh giá
            $products->getCollection()->transform(function ($product) {
                $product->min_price = $product->min_price;
                $product->min_discounted_price = $product->min_discounted_price;
                $product->average_rating = $product->average_rating;
                $product->review_count = $product->review_count;
                return $product;
            });

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin chi tiết của một sản phẩm
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $product = Product::where('slug', $slug)
                ->where('is_active', true)
                ->with([
                    'category:id,name,slug',
                    'weights' => function ($query) {
                        $query->where('is_active', true)->orderBy('is_default', 'desc');
                    },
                    'images' => function ($query) {
                        $query->orderBy('sort_order');
                    },
                    'reviews' => function ($query) {
                        $query->latest()->limit(5); // 5 reviews mới nhất
                    }
                ])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy đánh giá của sản phẩm với phân trang
     *
     * @param  string  $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviews($slug, Request $request)
    {
        try {
            $product = Product::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            $perPage = min(max((int)$request->input('per_page', 10), 1), 50);
            $reviews = $product->reviews()
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'average_rating' => $product->average_rating,
                    'review_count' => $product->review_count,
                    'reviews' => $reviews
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tìm kiếm sản phẩm
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            if (!$request->has('q')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập từ khóa tìm kiếm'
                ], 400);
            }

            $query = $request->q;
            $perPage = min(max((int)$request->input('per_page', 12), 1), 50);

            $products = Product::where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%');
                })
                ->with('category:id,name,slug')
                ->select('id', 'category_id', 'name', 'slug', 'image', 'is_featured')
                ->paginate($perPage);

            // Thêm thông tin giá và đánh giá
            $products->getCollection()->transform(function ($product) {
                $product->min_price = $product->min_price;
                $product->min_discounted_price = $product->min_discounted_price;
                return $product;
            });

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy sản phẩm nổi bật
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        try {
            // Số lượng sản phẩm cần trả về
            $totalProductsNeeded = 10;

            // Lấy sản phẩm nổi bật trước
            $featuredProducts = Product::where('is_active', true)
                ->where('is_featured', true)
                ->with('category:id,name,slug')
                ->select('id', 'category_id', 'name', 'slug', 'image','created_at','updated_at','description','highlight','is_featured')
                ->get();

            // Chuyển đổi dữ liệu để thêm các trường cần thiết
            $featuredProducts->transform(function ($product) {
                $product->min_price = $product->min_price;
                $product->min_discounted_price = $product->min_discounted_price;
                $product->average_rating = $product->average_rating;
                return $product;
            });

            // Nếu chưa đủ số lượng sản phẩm yêu cầu, thêm sản phẩm mới nhất
            if ($featuredProducts->count() < $totalProductsNeeded) {
                // Lấy ID của các sản phẩm nổi bật đã có
                $existingIds = $featuredProducts->pluck('id')->toArray();

                // Số lượng sản phẩm cần bổ sung thêm
                $additionalNeeded = $totalProductsNeeded - $featuredProducts->count();

                // Lấy thêm sản phẩm mới nhất (không trùng với các sản phẩm đã lấy)
                $newProducts = Product::where('is_active', true)
                    ->whereNotIn('id', $existingIds)
                    ->with('category:id,name,slug')
                    ->select('id', 'category_id', 'name', 'slug', 'image','created_at','updated_at','description','highlight','is_featured')
                    ->latest()
                    ->limit($additionalNeeded)
                    ->get();

                // Chuyển đổi dữ liệu các sản phẩm mới
                $newProducts->transform(function ($product) {
                    $product->min_price = $product->min_price;
                    $product->min_discounted_price = $product->min_discounted_price;
                    $product->average_rating = $product->average_rating;
                    return $product;
                });

                // Gộp hai danh sách sản phẩm lại
                $featuredProducts = $featuredProducts->concat($newProducts);
            } else if ($featuredProducts->count() > $totalProductsNeeded) {
                // Nếu có quá nhiều sản phẩm nổi bật, chỉ lấy số lượng cần thiết
                $featuredProducts = $featuredProducts->take($totalProductsNeeded);
            }

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($featuredProducts)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
