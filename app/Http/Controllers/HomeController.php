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

        // Sản phẩm bán chạy nhất
        $likeProducts = (clone $query)->latest()->take(8)->get();

        // Sản phẩm mới nhất
        $relatedProducts = (clone $query)->latest()->take(8)->get();
        
        return view('client.pages.product-detail', compact('relatedProducts', 'likeProducts'));
    }

    public function categoryProducts($slug)
    {
        return view('client.pages.category');
    }
}
