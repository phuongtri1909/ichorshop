<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Category;
use App\Models\DressStyle;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class FilterProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('components.product-filters', function ($view) {
            // Categories và DressStyles có thể ít thay đổi nên có thể lấy tất cả
            $categories = Category::all();
            $dressStyles = DressStyle::all();
            
            // Sử dụng Cache để lưu danh sách colors và sizes để tối ưu hiệu suất
            $colors = Cache::remember('product_colors', 60*24, function () {
                // Sử dụng truy vấn raw để lấy danh sách màu duy nhất từ variants của các sản phẩm active
                return DB::table('product_variants')
                    ->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->where('products.status', 'active')
                    ->where('product_variants.status', ProductVariant::STATUS_ACTIVE)
                    ->whereNull('product_variants.deleted_at')
                    ->select('product_variants.color', 'product_variants.color_name')
                    ->distinct()
                    ->orderBy('product_variants.color_name')
                    ->get()
                    ->map(function($item) {
                        return [
                            'value' => $item->color,
                            'name' => $item->color_name
                        ];
                    });
            });
            
            $sizes = Cache::remember('product_sizes', 60*24, function () {
                // Sử dụng truy vấn raw để lấy danh sách kích thước duy nhất từ variants của các sản phẩm active
                return DB::table('product_variants')
                    ->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->where('products.status', 'active')
                    ->where('product_variants.status', ProductVariant::STATUS_ACTIVE)
                    ->whereNull('product_variants.deleted_at')
                    ->select('product_variants.size')
                    ->distinct()
                    ->orderBy('product_variants.size')
                    ->pluck('size')
                    ->map(function($size) {
                        return [
                            'value' => $size,
                            'name' => $size
                        ];
                    });
            });
            
            // Lấy phạm vi giá (min-max) từ các biến thể sản phẩm có tính đến giá đã giảm
            $priceRange = Cache::remember('product_price_range', 60*24, function () {
                $now = now();
                
                // Truy vấn lấy giá gốc thấp nhất và cao nhất
                $originalPriceRange = DB::table('product_variants')
                    ->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->where('products.status', 'active')
                    ->where('product_variants.status', ProductVariant::STATUS_ACTIVE)
                    ->whereNull('product_variants.deleted_at')
                    ->selectRaw('MIN(product_variants.price) as min_price, MAX(product_variants.price) as max_price')
                    ->first();
                
                // Lấy mức giảm giá lớn nhất theo phần trăm
                $maxPercentageDiscount = DB::table('promotions')
                    ->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->where('type', Promotion::TYPE_PERCENTAGE)
                    ->max('value');
                
                // Lấy mức giảm giá cố định lớn nhất
                $maxFixedDiscount = DB::table('promotions')
                    ->where('status', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->where('type', Promotion::TYPE_FIXED)
                    ->max('value');
                
                // Tính toán giá thấp nhất có thể sau khi giảm giá
                $minDiscountedPrice = $originalPriceRange->min_price;
                if ($maxPercentageDiscount) {
                    $percentageDiscount = $originalPriceRange->min_price * ($maxPercentageDiscount / 100);
                    $minDiscountedPrice = min($minDiscountedPrice, $originalPriceRange->min_price - $percentageDiscount);
                }
                if ($maxFixedDiscount) {
                    $minDiscountedPrice = min($minDiscountedPrice, max(0, $originalPriceRange->min_price - $maxFixedDiscount));
                }
                
                // Làm tròn giá để tạo khoảng giá phù hợp
                $minPrice = floor($minDiscountedPrice / 100) * 100;
                $maxPrice = ceil($originalPriceRange->max_price / 100) * 100;
                
                return [
                    'min' => max(0, $minPrice),
                    'max' => $maxPrice
                ];
            });

            $view->with(compact('categories', 'dressStyles', 'colors', 'sizes', 'priceRange'));
        });
    }
    
    /**
     * Custom method to update price range cache specifically
     * Can be called from commands or when prices change significantly
     */
    public static function updatePriceRangeCache()
    {
        Cache::forget('product_price_range');
        return self::calculatePriceRange();
    }
    
    /**
     * Calculate price range without using cache
     */
    public static function calculatePriceRange()
    {
        $now = now();
        
        // Truy vấn lấy giá gốc thấp nhất và cao nhất
        $originalPriceRange = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('products.status', 'active')
            ->where('product_variants.status', ProductVariant::STATUS_ACTIVE)
            ->whereNull('product_variants.deleted_at')
            ->selectRaw('MIN(product_variants.price) as min_price, MAX(product_variants.price) as max_price')
            ->first();
        
        // Lấy mức giảm giá lớn nhất theo phần trăm
        $maxPercentageDiscount = DB::table('promotions')
            ->where('status', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('type', Promotion::TYPE_PERCENTAGE)
            ->max('value');
        
        // Lấy mức giảm giá cố định lớn nhất
        $maxFixedDiscount = DB::table('promotions')
            ->where('status', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('type', Promotion::TYPE_FIXED)
            ->max('value');
        
        // Tính toán giá thấp nhất có thể sau khi giảm giá
        $minDiscountedPrice = $originalPriceRange->min_price;
        if ($maxPercentageDiscount) {
            $percentageDiscount = $originalPriceRange->min_price * ($maxPercentageDiscount / 100);
            $minDiscountedPrice = min($minDiscountedPrice, $originalPriceRange->min_price - $percentageDiscount);
        }
        if ($maxFixedDiscount) {
            $minDiscountedPrice = min($minDiscountedPrice, max(0, $originalPriceRange->min_price - $maxFixedDiscount));
        }
        
        // Làm tròn giá để tạo khoảng giá phù hợp
        $minPrice = floor($minDiscountedPrice / 100) * 100;
        $maxPrice = ceil($originalPriceRange->max_price / 100) * 100;
        
        return [
            'min' => max(0, $minPrice),
            'max' => $maxPrice
        ];
    }
}
