<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\DressStyle;
use App\Models\Product;
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

        // Nếu không có trường total_sales, có thể dùng latest() tạm thời
        $topSellingProducts = (clone $query)->latest()->take(8)->get();

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

        $cheapestVariant = $product->variants
            ->sortBy(fn($v) => $v->getDiscountedPrice())
            ->first();

        $defaultColor = $cheapestVariant->color_name ?? null;
        $defaultColorCode = $cheapestVariant->color ?? null;
        $defaultSize = $cheapestVariant->size ?? null;

        $availableColors = $product->variants
            ->whereNotNull('color')
            ->whereNotNull('color_name')
            ->pluck('color', 'color_name')
            ->unique();

        $availableSizes = $product->variants
            ->pluck('size')
            ->unique()
            ->values();

        // $sizesByColor = $product->variants
        //     ->whereNotNull('color')
        //     ->groupBy('color')
        //     ->map(fn($group) => $group->pluck('size')->unique()->values());

        // $colorsBySize = $product->variants
        //     ->whereNotNull('color')
        //     ->whereNotNull('color_name')
        //     ->groupBy('size')
        //     ->map(fn($group) => $group->pluck('color', 'color_name')->unique());

        // $sizesWithoutColor = $product->variants
        //     ->whereNull('color')
        //     ->pluck('size')
        //     ->unique()
        //     ->values();

        $productImages = collect();
        if ($defaultColorCode) {
            $productImages = $product->images->where('color', $defaultColorCode);
        }
        if ($productImages->isEmpty()) {
            $productImages = $product->images->whereNull('color');
        }
        if ($productImages->isEmpty()) {
            $productImages = collect([$product->avatar_url]);
        } else {
            $productImages = $productImages->pluck('image_url');
        }

        $currentPrice = $cheapestVariant?->getDiscountedPrice() ?? 0;
        $originalPrice = $cheapestVariant->price ?? 0;

        $discount = $originalPrice > $currentPrice
            ? round(100 - ($currentPrice / $originalPrice * 100))
            : 0;

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
            'colors' => $availableColors->toArray(),
            'sizes' => $availableSizes,

            // 'sizes_by_color' => $sizesByColor,
            // 'colors_by_size' => $colorsBySize,

            // 'sizes_without_color' => $sizesWithoutColor,
            'default_color' => $defaultColor,
            'default_color_code' => $defaultColorCode,
            'default_size' => $defaultSize,
            'cheapest_variant' => $cheapestVariant?->toArray(),
            'all_variants' => $product->variants->toArray(),
        ];

        //dd($productData);

        return view('client.pages.product-detail', [
            'product' => $productData,
            'breadcrumbItems' => $breadcrumbItems,
            'likeProducts' => $likeProducts,
            'relatedProducts' => $relatedProducts,
        ]);
    }


    public function categoryProducts($slug)
    {
        return view('client.pages.category');
    }
}
