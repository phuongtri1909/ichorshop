<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\DressStyle;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function getProductImages($slug, $color = null)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        
        // Xử lý trường hợp color là "null" từ query string
        if ($color === "null") {
            $color = null;
        }
        
        $images = $product->images()
            ->when(isset($color), function($query) use ($color) {
                return $query->where('color', $color);
            })
            ->get();
        
        // Nếu không tìm thấy ảnh với màu cụ thể, lấy ảnh không có màu
        if ($images->isEmpty() && isset($color)) {
            $images = $product->images()
                ->whereNull('color')
                ->get();
        }
        
        // Nếu vẫn không có ảnh, sử dụng avatar của sản phẩm
        if ($images->isEmpty()) {
            return response()->json([
                'images' => [$product->avatar_url]
            ]);
        }
        
        return response()->json([
            'images' => $images->pluck('image_url')
        ]);
    }
}
