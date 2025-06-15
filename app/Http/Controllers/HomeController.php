<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Brand;
use App\Models\Product;
use App\Models\DressStyle;
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

        return view('client.pages.home', [
            'brands' => $brands,
            'brandCount' => $brandCount,
            'productCount' => $productCount,
            'newProducts' => $newProducts,
            'topSellingProducts' => $topSellingProducts,
            'customerCount' => $customerCount,
            'styles' => $styles
        ]);
    }

    public function productDetails($slug)
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

        $likeProducts = Product::with([
            'variants' => fn($query) => $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price'),
            'variants.variantPromotions.promotion' => fn($query) => $query->where('status', true)->where('start_date', '<=', $now)->where('end_date', '>=', $now)
        ])->where('id', '!=', $product->id)->active()->latest()->take(8)->get();

        $relatedProducts = [];
        $categoryIds = $product->categories->pluck('id');
        if ($categoryIds->isNotEmpty()) {
            $relatedProducts = Product::with([
                'variants' => fn($query) => $query->where('status', ProductVariant::STATUS_ACTIVE)->orderBy('price'),
                'variants.variantPromotions.promotion' => fn($query) => $query->where('status', true)->where('start_date', '<=', $now)->where('end_date', '>=', $now)
            ])->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds))
                ->where('id', '!=', $product->id)
                ->active()
                ->inRandomOrder()
                ->take(8)
                ->get();
        }

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
            'available_sizes' => $allSizes, // Initially all sizes are available

            'variants_without_color' => $variantsWithoutColor->toArray(),
            'default_color' => $defaultColor,
            'default_color_code' => $defaultColorCode,
            'default_size' => $defaultSize,
            'cheapest_variant' => $cheapestVariant?->toArray(),
            'all_variants' => $formattedVariants->toArray(),
        ];

        $faqs = Faq::orderBy('order')->take(4)->get();
        $totalFaqs = Faq::count();

        return view('client.pages.product-detail', [
            'product' => $productData,
            'breadcrumbItems' => $breadcrumbItems,
            'likeProducts' => $likeProducts,
            'relatedProducts' => $relatedProducts,
            'faqs' => $faqs,
            'totalFaqs' => $totalFaqs,
        ]);
    }


    public function categoryProducts($slug)
    {
        return view('client.pages.category');
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
}
