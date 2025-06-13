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
