<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        $brands = Brand::all();
        $brandCount = $brands->count();

        $query = Product::active();

        $productCount = (clone $query)->count();
        $newProducts = $query->latest()->take(8)->get();

        $topSellingProducts = $query->latest()->take(8)->get();

        $customerCount = 0;

        return view('client.pages.home', [
            'brands' => $brands,
            'brandCount' => $brandCount,
            'productCount' => $productCount,
            'newProducts' => $newProducts,
            'topSellingProducts' => $topSellingProducts,
            'customerCount' => $customerCount,
        ]);
    }

    public function productDetails($slug)
    {

        return view('client.pages.product-detail');
    }

    public function categoryProducts($slug)
    {
        return view('client.pages.category');
    }
}
