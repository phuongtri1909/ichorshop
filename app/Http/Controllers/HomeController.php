<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Brand;
use App\Models\Product;
use App\Models\DressStyle;
use App\Models\ProductView;
use App\Models\ReviewRating;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        $brands = Brand::all();
        $brandCount = $brands->count();

        // Query cho sản phẩm active
        $query = Product::with([
            'variants' => function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE)
                    ->orderBy('price', 'asc');
            },
            'variants.variantPromotions.promotion' => function ($query) {
                $now = now();
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            }
        ])->active();

        $productCount = (clone $query)->count();
        // Sản phẩm mới nhất
        $newProducts = (clone $query)->latest()->take(8)->get();

        $now = now();

        $topSellingProducts = Product::with([
            'variants' => function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE)
                    ->orderBy('price', 'asc');
            },
            'variants.variantPromotions.promotion' => function ($query) use ($now) {
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->orderBy('created_at', 'desc');
            }
        ])
            ->active()
            ->whereHas('variants', function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE);
            })
            ->whereHas('variants.variantPromotions.promotion', function ($query) use ($now) {
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            })
            ->select(['products.*', \DB::raw('(SELECT MAX(promotions.created_at) 
                                     FROM promotions 
                                     JOIN product_variant_promotions ON promotions.id = product_variant_promotions.promotion_id 
                                     JOIN product_variants ON product_variant_promotions.product_variant_id = product_variants.id 
                                     WHERE product_variants.product_id = products.id 
                                     AND promotions.status = true 
                                     AND promotions.start_date <= NOW() 
                                     AND promotions.end_date >= NOW()) as latest_promotion')])
            ->orderBy('latest_promotion', 'desc')
            ->take(8)
            ->get();

        $customerCount = 0;


        $styles = DressStyle::get();

        $latestReviews = ReviewRating::with('user', 'product')
            ->published()
            ->whereDate('created_at', '>=', now()->subMonth())
            ->latest()
            ->take(30)
            ->get();

        return view('client.pages.home', [
            'brands' => $brands,
            'brandCount' => $brandCount,
            'productCount' => $productCount,
            'newProducts' => $newProducts,
            'topSellingProducts' => $topSellingProducts,
            'customerCount' => $customerCount,
            'styles' => $styles,
            'latestReviews' => $latestReviews
        ]);
    }

    public function productDetails($slug, Request $request)
    {
        $now = now();

        $product = Product::with([
            'variants' => function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
            },
            'variants.variantPromotions.promotion' => function ($query) use ($now) {
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            },
            'images',
            'categories',
            'dressStyles',
            'brand'
        ])->where('slug', $slug)->active()->firstOrFail();

        // Ghi lại lượt xem sản phẩm
        $userId = Auth::check() ? Auth::id() : null;
        $ipAddress = request()->ip();
        $product->recordView($userId, $ipAddress);


        $this->saveRecentlyViewed($product->id);

        // Find cheapest variant after discounts
        $cheapestVariant = $product->variants
            ->sortBy(fn($v) => $v->getDiscountedPrice())
            ->first();

        // Set default selections based on cheapest variant
        $defaultColor = $cheapestVariant->color_name ?? null;
        $defaultColorCode = $cheapestVariant->color ?? null;
        $defaultSize = $cheapestVariant->size ?? null;

        // Get available colors (with their codes)
        $availableColors = $product->variants
            ->whereNotNull('color')
            ->whereNotNull('color_name')
            ->pluck('color', 'color_name')
            ->unique();

        // Get all sizes from all variants
        $allSizes = $product->variants
            ->pluck('size')
            ->filter() // Remove null values
            ->unique()
            ->values();

        // Get variants without color (for "Default" option)
        $variantsWithoutColor = $product->variants
            ->whereNull('color')
            ->whereNull('color_name')
            ->values();

        // Get images
        $defaultImages = $product->images->whereNull('color')->pluck('image_url');
        if ($defaultImages->isEmpty()) {
            $defaultImages = collect([$product->avatar_url]);
        }

        // Get images per color
        $colorImages = [];
        foreach ($availableColors as $colorName => $colorCode) {
            $images = $product->images->where('color', $colorCode)->pluck('image_url');
            if ($images->isNotEmpty()) {
                $colorImages[$colorName] = $images->toArray();
            }
        }

        // Initial product images based on default color
        $productImages = collect();
        if ($defaultColorCode && isset($colorImages[$defaultColor])) {
            $productImages = collect($colorImages[$defaultColor]);
        } else {
            $productImages = $defaultImages;
        }

        // Price calculations
        $currentPrice = $cheapestVariant?->getDiscountedPrice() ?? 0;
        $originalPrice = $cheapestVariant->price ?? 0;

        $discount = $originalPrice > $currentPrice
            ? round(100 - ($currentPrice / $originalPrice * 100))
            : 0;

        // Format variants for frontend with discounted prices
        $formattedVariants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'color' => $variant->color,
                'color_name' => $variant->color_name,
                'size' => $variant->size,
                'price' => (float)$variant->price, // Ensure price is numeric
                'discounted_price' => (float)$variant->getDiscountedPrice(), // Ensure discounted_price is numeric
                'sku' => $variant->sku,
                'quantity' => $variant->quantity,
            ];
        });

        $breadcrumbItems = [
            ['title' => 'Home', 'url' => route('home')],
        ];

        if ($product->categories->isNotEmpty()) {
            $category = $product->categories->first();
            $breadcrumbItems[] = [
                'title' => $category->name,
                'url' => route('category.products', $category->slug)
            ];
        }

        $breadcrumbItems[] = [
            'title' => $product->name,
            'url' => null,
            'active' => true
        ];

        // Lấy sản phẩm "Bạn có thể thích" dựa trên lượt xem
        $likeProducts = $this->getYouMightAlsoLikeProducts($product->id);


        // Lấy các sản phẩm liên quan
        $relatedProducts = $this->getRelatedProducts($product);

        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'rating' => 4.5,
            'reviews_count' => 0,
            'description' => $product->description_short,
            'description_long' => $product->description_long,

            'current_price' => $currentPrice,
            'original_price' => $originalPrice != $currentPrice ? $originalPrice : null,
            'discount' => $discount,

            'images' => $productImages->toArray(),
            'default_images' => $defaultImages->toArray(),
            'color_images' => $colorImages,

            'colors' => $availableColors->toArray(),
            'all_sizes' => $allSizes,
            'available_sizes' => $allSizes,

            'variants_without_color' => $variantsWithoutColor->toArray(),
            'default_color' => $defaultColor,
            'default_color_code' => $defaultColorCode,
            'default_size' => $defaultSize,
            'cheapest_variant' => $cheapestVariant?->toArray(),
            'all_variants' => $formattedVariants->toArray(),
        ];

        $faqs = Faq::orderBy('order')->take(4)->get();
        $totalFaqs = Faq::count();

        $sortBy = $request->query('sort', 'latest');

        $reviewsQuery = ReviewRating::with('user')
            ->where('product_id', $product->id)
            ->published();

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

        $reviews = $reviewsQuery->paginate(6);

        // Tính số lượng đánh giá và điểm trung bình
        $reviewsCount = ReviewRating::where('product_id', $product->id)->published()->count();
        $averageRating = $reviewsCount > 0 ?
            round(ReviewRating::where('product_id', $product->id)->published()->avg('rating'), 1) : 0;

        $ratingCounts = ReviewRating::where('product_id', $product->id)
            ->published()
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();



        return view('client.pages.product-detail', [
            'product' => $productData,
            'breadcrumbItems' => $breadcrumbItems,
            'likeProducts' => $likeProducts,
            'relatedProducts' => $relatedProducts,
            'faqs' => $faqs,
            'totalFaqs' => $totalFaqs,
            'reviews' => $reviews,
            'reviewsCount' => $reviewsCount,
            'averageRating' => $averageRating,
            'ratingCounts' => $ratingCounts,
            'currentSort' => $sortBy
        ]);
    }

    /**
     * Lưu sản phẩm đã xem vào trong session
     */
    private function saveRecentlyViewed($productId)
    {
        // Lấy danh sách sản phẩm đã xem từ session
        $recentlyViewed = session()->get('recently_viewed', []);

        // Nếu sản phẩm đã có trong danh sách, xóa nó đi để đưa lên đầu danh sách
        if (($key = array_search($productId, $recentlyViewed)) !== false) {
            unset($recentlyViewed[$key]);
        }

        // Thêm sản phẩm mới vào đầu danh sách
        array_unshift($recentlyViewed, $productId);

        // Giới hạn chỉ lưu tối đa 20 sản phẩm gần đây
        $recentlyViewed = array_slice($recentlyViewed, 0, 20);

        // Lưu lại vào session
        session()->put('recently_viewed', $recentlyViewed);
    }

    private function applyProductFilters($query, Request $request)
    {
        // Filter theo category
        if ($request->has('categories') && !empty($request->categories)) {
            $categoryIds = is_array($request->categories) ? $request->categories : explode(',', $request->categories);
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        // Filter theo style
        if ($request->has('styles') && !empty($request->styles)) {
            $styleIds = is_array($request->styles) ? $request->styles : explode(',', $request->styles);
            $query->whereHas('dressStyles', function ($q) use ($styleIds) {
                $q->whereIn('dress_styles.id', $styleIds);
            });
        }

        // Filter theo color
        if ($request->has('colors') && !empty($request->colors)) {
            $colors = is_array($request->colors) ? $request->colors : explode(',', $request->colors);

            $colors = array_map(function ($color) {
                return $color === 'null' ? null : $color;
            }, $colors);

            $query->whereHas('variants', function ($q) use ($colors) {
                $q->where(function ($subQuery) use ($colors) {
                    foreach ($colors as $color) {
                        if ($color === null) {
                            $subQuery->orWhereNull('color');
                        } else {
                            $subQuery->orWhere('color', $color);
                        }
                    }
                });
            });
        }

        // Filter theo size
        if ($request->has('sizes') && !empty($request->sizes)) {
            $sizes = is_array($request->sizes) ? $request->sizes : explode(',', $request->sizes);
            $query->whereHas('variants', function ($q) use ($sizes) {
                $q->whereIn('size', $sizes);
            });
        }

        // Filter theo price range
        if ($request->has('price_min') && $request->has('price_max')) {
            $minPrice = (float) $request->price_min;
            $maxPrice = (float) $request->price_max;

            // Chỉ áp dụng filter khi không phải là khoảng giá mặc định (0 đến giá cao nhất)
            $defaultMaxPrice = 100000000; // Một giá trị đủ lớn để đại diện cho "giá cao nhất"
            $isDefaultRange = ($minPrice <= 0 && $maxPrice >= $defaultMaxPrice);

            if (!$isDefaultRange) {
                $query->whereHas('variants', function ($q) use ($minPrice, $maxPrice) {
                    $q->where(function ($subQuery) use ($minPrice, $maxPrice) {
                        // Kiểm tra giá sau khi giảm giá
                        $now = now();

                        // Sản phẩm có promotion
                        $subQuery->where(function ($sq1) use ($minPrice, $maxPrice, $now) {
                            $sq1->whereHas('variantPromotions.promotion', function ($sq2) use ($now) {
                                $sq2->where('status', true)
                                    ->where('start_date', '<=', $now)
                                    ->where('end_date', '>=', $now);
                            })
                                ->whereRaw(
                                    "
                        (CASE
                            WHEN EXISTS (
                                SELECT 1 FROM promotions p
                                JOIN product_variant_promotions pvp ON p.id = pvp.promotion_id
                                WHERE pvp.product_variant_id = product_variants.id
                                AND p.status = true
                                AND p.start_date <= ?
                                AND p.end_date >= ?
                                AND p.type = 'percentage'
                                ORDER BY p.value DESC
                                LIMIT 1
                            ) THEN
                                (
                                    SELECT product_variants.price * (1 - (p.value / 100))
                                    FROM promotions p
                                    JOIN product_variant_promotions pvp ON p.id = pvp.promotion_id
                                    WHERE pvp.product_variant_id = product_variants.id
                                    AND p.status = true
                                    AND p.start_date <= ?
                                    AND p.end_date >= ?
                                    AND p.type = 'percentage'
                                    ORDER BY p.value DESC
                                    LIMIT 1
                                )
                            WHEN EXISTS (
                                SELECT 1 FROM promotions p
                                JOIN product_variant_promotions pvp ON p.id = pvp.promotion_id
                                WHERE pvp.product_variant_id = product_variants.id
                                AND p.status = true
                                AND p.start_date <= ?
                                AND p.end_date >= ?
                                AND p.type = 'fixed'
                                ORDER BY p.value DESC
                                LIMIT 1
                            ) THEN
                                (
                                    SELECT product_variants.price - p.value
                                    FROM promotions p
                                    JOIN product_variant_promotions pvp ON p.id = pvp.promotion_id
                                    WHERE pvp.product_variant_id = product_variants.id
                                    AND p.status = true
                                    AND p.start_date <= ?
                                    AND p.end_date >= ?
                                    AND p.type = 'fixed'
                                    ORDER BY p.value DESC
                                    LIMIT 1
                                )
                            ELSE product_variants.price
                        END
                        ) BETWEEN ? AND ?",
                                    [$now, $now, $now, $now, $now, $now, $now, $now, $minPrice, $maxPrice]
                                );
                        })
                            ->orWhere(function ($sq1) use ($minPrice, $maxPrice) {
                                // Sản phẩm không có promotion - giá gốc trong khoảng
                                $sq1->whereDoesntHave('variantPromotions')
                                    ->whereBetween('price', [$minPrice, $maxPrice]);
                            });
                    });
                });
            }
        }

        // Sort options
        // if ($request->has('sort')) {
        //     switch ($request->sort) {
        //         case 'price-low':
        //             $query->orderBy(\DB::raw('(SELECT MIN(pv.price) FROM product_variants pv WHERE pv.product_id = products.id AND pv.status = "active")'), 'asc');
        //             break;

        //         case 'price-high':
        //             $query->orderBy(\DB::raw('(SELECT MAX(pv.price) FROM product_variants pv WHERE pv.product_id = products.id AND pv.status = "active")'), 'desc');
        //             break;

        //         case 'newest':
        //             $query->latest();
        //             break;

        //         case 'rating':
        //             $query->orderBy('rating', 'desc');
        //             break;

        //         case 'most-popular':
        //         default:
        //             $query->orderBy('view_count', 'desc');
        //             break;
        //     }
        // }

        return $query;
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('q');

        $now = now();

        $query = Product::with([
            'variants' => function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
            },
            'variants.variantPromotions.promotion' => function ($query) use ($now) {
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            },
            'categories',
            'dressStyles'
        ])->active();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description_short', 'like', "%{$searchTerm}%")
                    ->orWhere('description_long', 'like', "%{$searchTerm}%");
            });
        }

        $query = $this->applyProductFilters($query, $request);

        if (!$request->has('sort')) {
            $query->latest();
        }

        $products = $query->paginate(20)->withQueryString();

        $appliedFilters = $this->getAppliedFilters($request);

        $breadcrumbItems = [
            ['title' => 'Home', 'url' => route('home')],
            ['title' => 'Search Results', 'url' => null, 'active' => true]
        ];

        return view('client.pages.filter.search_results', [
            'products' => $products,
            'appliedFilters' => $appliedFilters,
            'searchTerm' => $searchTerm,
            'breadcrumbItems' => $breadcrumbItems
        ]);
    }

    public function categoryProducts($slug, Request $request)
    {
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();

        $now = now();

        // Query sản phẩm với các quan hệ cần thiết
        $query = Product::with([
            'variants' => function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
            },
            'variants.variantPromotions.promotion' => function ($query) use ($now) {
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            },
            'categories',
            'dressStyles'
        ])
            ->active()
            // Lấy sản phẩm thuộc về danh mục này hoặc các danh mục con
            ->where(function ($query) use ($category) {
                // Lấy danh mục hiện tại
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('categories.id', $category->id);
                });

                // Nếu có các danh mục con, lấy cả sản phẩm thuộc danh mục con
                if ($category->children && $category->children->count() > 0) {
                    $childrenIds = $category->children->pluck('id')->toArray();
                    $query->orWhereHas('categories', function ($q) use ($childrenIds) {
                        $q->whereIn('categories.id', $childrenIds);
                    });
                }
            });

        // Áp dụng các bộ lọc khác từ request (ngoại trừ category vì đã lọc theo slug)
        $modifiedRequest = clone $request;
        $modifiedRequest->offsetUnset('categories'); // Không áp dụng filter categories
        $query = $this->applyProductFilters($query, $modifiedRequest);

        // Sắp xếp mặc định nếu không có tham số sort
        if (!$request->has('sort')) {
            $query->latest(); // Mặc định sắp xếp theo thời gian tạo mới nhất
        }

        // Phân trang kết quả
        $products = $query->paginate(20)->withQueryString();

        // Lấy các bộ lọc đã áp dụng, loại bỏ category vì đã lọc theo slug
        $appliedFilters = $this->getAppliedFilters($modifiedRequest);

        // Lấy breadcrumbs cho danh mục hiện tại
        $breadcrumbItems = [
            ['title' => 'Home', 'url' => route('home')],
        ];

        // Thêm danh mục cha nếu có
        if ($category->parent) {
            $breadcrumbItems[] = [
                'title' => $category->parent->name,
                'url' => route('category.products', $category->parent->slug)
            ];
        }

        // Thêm danh mục hiện tại
        $breadcrumbItems[] = [
            'title' => $category->name,
            'url' => null,
            'active' => true
        ];

        return view('client.pages.filter.category', [
            'category' => $category,
            'products' => $products,
            'appliedFilters' => $appliedFilters,
            'breadcrumbItems' => $breadcrumbItems,
            'showCategoryFilter' => false
        ]);
    }

    public function newArrivals(Request $request)
    {
        $query = Product::with([
            'variants' => function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
            },
            'variants.variantPromotions.promotion' => function ($query) {
                $now = now();
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            },
            'categories',
            'dressStyles'
        ])->active();

        $query = $this->applyProductFilters($query, $request);

        if (!$request->has('sort')) {
            $query->latest();
        }

        $products = $query->paginate(20)->withQueryString();

        return view('client.pages.filter.new_arrivals', [
            'products' => $products,
            'appliedFilters' => $this->getAppliedFilters($request)
        ]);
    }

    public function topSelling(Request $request)
    {
        $now = now();

        $query = Product::with([
            'variants' => function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
            },
            'variants.variantPromotions.promotion' => function ($query) use ($now) {
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            },
            'categories',
            'dressStyles'
        ])
            ->active()
            ->whereHas('variants', function ($query) {
                $query->where('status', ProductVariant::STATUS_ACTIVE);
            })
            ->whereHas('variants.variantPromotions.promotion', function ($query) use ($now) {
                $query->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            });

        // Áp dụng filters từ request
        $query = $this->applyProductFilters($query, $request);

        // Mặc định sắp xếp theo giảm giá mới nhất
        if (!$request->has('sort')) {
            $query->orderBy(\DB::raw('(SELECT MAX(promotions.created_at) 
                                FROM promotions 
                                JOIN product_variant_promotions ON promotions.id = product_variant_promotions.promotion_id 
                                JOIN product_variants ON product_variant_promotions.product_variant_id = product_variants.id 
                                WHERE product_variants.product_id = products.id 
                                AND promotions.status = true 
                                AND promotions.start_date <= NOW() 
                                AND promotions.end_date >= NOW())'), 'desc');
        }

        $products = $query->paginate(20)->withQueryString();

        return view('client.pages.filter.top_selling', [
            'products' => $products,
            'appliedFilters' => $this->getAppliedFilters($request)
        ]);
    }

    // Phương thức để lấy các filter đã áp dụng để hiển thị trên UI
    private function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->has('categories')) {
            $filters['categories'] = is_array($request->categories) ? $request->categories : explode(',', $request->categories);
        }

        if ($request->has('styles')) {
            $filters['styles'] = is_array($request->styles) ? $request->styles : explode(',', $request->styles);
        }

        if ($request->has('colors')) {
            $filters['colors'] = is_array($request->colors) ? $request->colors : explode(',', $request->colors);
        }

        if ($request->has('sizes')) {
            $filters['sizes'] = is_array($request->sizes) ? $request->sizes : explode(',', $request->sizes);
        }

        if ($request->has('price_min') && $request->has('price_max')) {
            $filters['price_min'] = (float) $request->price_min;
            $filters['price_max'] = (float) $request->price_max;
        }

        if ($request->has('sort')) {
            $filters['sort'] = $request->sort;
        }

        return $filters;
    }

    /**
     * Lấy danh sách sản phẩm "Bạn có thể thích"
     * Kết hợp giữa sản phẩm người dùng xem nhiều và sản phẩm được xem nhiều nhất
     */
    private function getYouMightAlsoLikeProducts($currentProductId)
    {
        $now = now();
        $userId = Auth::check() ? Auth::id() : null;
        $ipAddress = request()->ip();

        $hasAnyViews = ProductView::count() > 0;

        // Nếu không có dữ liệu xem nào, trả về sản phẩm ngẫu nhiên hoặc mới nhất
        if (!$hasAnyViews) {
            return Product::with([
                'variants' => function ($query) {
                    $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
                },
                'variants.variantPromotions.promotion' => function ($query) use ($now) {
                    $query->where('status', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                }
            ])
                ->active()
                ->where('id', '!=', $currentProductId)
                ->latest()
                ->take(8)
                ->get();
        }

        // Lấy 4 sản phẩm người dùng hiện tại xem nhiều (nếu đã đăng nhập)
        $userViewProducts = collect();
        $userHasViews = false;

        if ($userId) {
            $userHasViews = ProductView::where('user_id', $userId)->exists();

            // Chỉ truy vấn nếu user có lượt xem
            if ($userHasViews) {
                // Lấy theo user_id
                $userViewProducts = Product::with([
                    'variants' => function ($query) {
                        $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
                    },
                    'variants.variantPromotions.promotion' => function ($query) use ($now) {
                        $query->where('status', true)
                            ->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now);
                    }
                ])
                    ->active()
                    ->where('id', '!=', $currentProductId)
                    ->whereHas('productViews', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    })
                    ->withCount(['productViews as user_view_count' => function ($query) use ($userId) {
                        $query->where('user_id', $userId)
                            ->select(\DB::raw('SUM(view_count)'));
                    }])
                    ->orderByDesc('user_view_count')
                    ->take(4)
                    ->get();
            }
        } else {
            $userHasViews = ProductView::where('ip_address', $ipAddress)->exists();

            // Chỉ truy vấn nếu IP có lượt xem
            if ($userHasViews) {
                // Lấy theo ip_address cho khách chưa đăng nhập
                $userViewProducts = Product::with([
                    'variants' => function ($query) {
                        $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
                    },
                    'variants.variantPromotions.promotion' => function ($query) use ($now) {
                        $query->where('status', true)
                            ->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now);
                    }
                ])
                    ->active()
                    ->where('id', '!=', $currentProductId)
                    ->whereHas('productViews', function ($query) use ($ipAddress) {
                        $query->where('ip_address', $ipAddress);
                    })
                    ->withCount(['productViews as user_view_count' => function ($query) use ($ipAddress) {
                        $query->where('ip_address', $ipAddress)
                            ->select(\DB::raw('SUM(view_count)'));
                    }])
                    ->orderByDesc('user_view_count')
                    ->take(4)
                    ->get();
            }
        }

        // Lấy số lượng cần thiết để đủ 8 sản phẩm
        $neededCount = 8 - $userViewProducts->count();

        // Lấy sản phẩm được xem nhiều nhất của tất cả người dùng, nhưng không lặp lại với userViewProducts
        $popularProducts = collect();

        if ($neededCount > 0) {
            $popularProducts = Product::with([
                'variants' => function ($query) {
                    $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
                },
                'variants.variantPromotions.promotion' => function ($query) use ($now) {
                    $query->where('status', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                }
            ])
                ->active()
                ->where('id', '!=', $currentProductId)
                ->whereNotIn('id', $userViewProducts->pluck('id')->toArray())
                ->whereHas('productViews')
                ->withCount(['productViews as total_view_count' => function ($query) {
                    $query->select(\DB::raw('SUM(view_count)'));
                }])
                ->orderByDesc('total_view_count')
                ->take($neededCount)
                ->get();
        }

        // Nếu vẫn chưa đủ 8 sản phẩm (do không đủ sản phẩm có lượt xem), 
        // bổ sung bằng sản phẩm mới nhất
        $remainingCount = 8 - ($userViewProducts->count() + $popularProducts->count());
        $additionalProducts = collect();

        if ($remainingCount > 0) {
            $existingIds = $userViewProducts->pluck('id')
                ->concat($popularProducts->pluck('id'))
                ->push($currentProductId)
                ->toArray();

            $additionalProducts = Product::with([
                'variants' => function ($query) {
                    $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price');
                },
                'variants.variantPromotions.promotion' => function ($query) use ($now) {
                    $query->where('status', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                }
            ])
                ->active()
                ->whereNotIn('id', $existingIds)
                ->latest()
                ->take($remainingCount)
                ->get();
        }

        return $userViewProducts->concat($popularProducts)->concat($additionalProducts);
    }

    /**
     * Lấy các sản phẩm liên quan từ cùng danh mục
     */
    private function getRelatedProducts($product)
    {
        $now = now();
        $categoryIds = $product->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Product::with([
            'variants' => fn($query) => $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price'),
            'variants.variantPromotions.promotion' => fn($query) => $query->where('status', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
        ])
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds))
            ->where('id', '!=', $product->id)
            ->active()
            ->inRandomOrder()
            ->take(8)
            ->get();
    }
}
