<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    /**
     * Get all banners
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $banners = Banner::orderBy('sort_order', 'asc')->get();
            
            $items = $banners->map(function($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'description' => $banner->description,
                    'image' => $banner->image ? asset('storage/' . $banner->image) : null,
                    'link' => $banner->link,
                    'sort_order' => $banner->sort_order,
                    'is_active' => $banner->is_active,
                    'created_at' => $banner->created_at,
                    'updated_at' => $banner->updated_at,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get only active banners
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function active(Request $request)
    {
        try {
            $query = Banner::where('is_active', true)
                ->orderBy('sort_order', 'asc');
                
            // Lấy giới hạn số lượng nếu có
            if ($request->has('limit') && is_numeric($request->limit)) {
                $limit = min(max((int)$request->limit, 1), 20); // Giới hạn từ 1-20 items
                $query->limit($limit);
            }
            
            $banners = $query->get();
                
            $items = $banners->map(function($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'description' => $banner->description,
                    'image' => $banner->image ? asset('storage/' . $banner->image) : null,
                    'link' => $banner->link,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
