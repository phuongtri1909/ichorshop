<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Lấy danh sách tất cả tin tức với phân trang và filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = News::where('is_active', true)
                ->select('id', 'title', 'slug', 'avatar', 'content', 'created_at', 'is_featured', 'thumbnail');

            // Filter theo featured
            if ($request->has('featured')) {
                $query->where('is_featured', filter_var($request->featured, FILTER_VALIDATE_BOOLEAN));
            }

            // Search theo tiêu đề
            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            // Sắp xếp
            $sortField = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $allowedSortFields = ['title', 'created_at'];

            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }

            // Phân trang
            $perPage = min(max((int)$request->input('per_page', 10), 1), 50); // Giới hạn 1-50 items/page
            $news = $query->paginate($perPage);
            
            // Chuyển đổi đường dẫn ảnh
            $items = $news->items();
            foreach ($items as $item) {
                $item->avatar = $item->avatar ? asset('storage/' . $item->avatar) : null;
                $item->thumbnail = $item->thumbnail ? asset('storage/' . $item->thumbnail) : null;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'current_page' => $news->currentPage(),
                    'data' => $items,
                    'first_page_url' => $news->url(1),
                    'from' => $news->firstItem(),
                    'last_page' => $news->lastPage(),
                    'last_page_url' => $news->url($news->lastPage()),
                    'links' => $news->linkCollection()->toArray(),
                    'next_page_url' => $news->nextPageUrl(),
                    'path' => $news->path(),
                    'per_page' => $news->perPage(),
                    'prev_page_url' => $news->previousPageUrl(),
                    'to' => $news->lastItem(),
                    'total' => $news->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin chi tiết của một bài viết
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $news = News::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            // Lấy bài viết liên quan (cùng trạng thái nổi bật hoặc mới nhất)
            $relatedNews = News::where('id', '!=', $news->id)
                ->where('is_active', true)
                ->where(function($query) use ($news) {
                    $query->where('is_featured', $news->is_featured)
                          ->orWhere('is_featured', true);
                })
                ->select('id', 'title', 'slug', 'avatar', 'content', 'created_at', 'is_featured', 'thumbnail')
                ->latest()
                ->limit(4)
                ->get();
                
            // Chuyển đổi đường dẫn ảnh cho bài viết liên quan
            foreach ($relatedNews as $related) {
                $related->avatar = $related->avatar ? asset('storage/' . $related->avatar) : null;
                $related->thumbnail = $related->thumbnail ? asset('storage/' . $related->thumbnail) : null;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $news->id,
                    'title' => $news->title,
                    'slug' => $news->slug,
                    'thumbnail' => $news->thumbnail ? asset('storage/' . $news->thumbnail) : null,
                    'avatar' => $news->avatar ? asset('storage/' . $news->avatar) : null,
                    'content' => $news->content,
                    'is_featured' => $news->is_featured,
                    'created_at' => $news->created_at,
                    'updated_at' => $news->updated_at,
                    'related_news' => $relatedNews
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài viết'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách bài viết nổi bật
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured(Request $request)
    {
        try {
            // Số lượng bài viết trên mỗi trang (mặc định là 6)
            $perPage = min(max((int)$request->input('per_page', 6), 1), 20);

            // Lấy bài viết nổi bật
            $featuredNews = News::where('is_active', true)
                ->where('is_featured', true)
                ->select('id', 'title', 'slug', 'avatar', 'content', 'created_at', 'is_featured', 'thumbnail')
                ->latest()
                ->paginate($perPage);
                
            // Chuyển đổi đường dẫn ảnh
            $items = $featuredNews->items();
            foreach ($items as $item) {
                $item->avatar = $item->avatar ? asset('storage/' . $item->avatar) : null;
                $item->thumbnail = $item->thumbnail ? asset('storage/' . $item->thumbnail) : null;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'current_page' => $featuredNews->currentPage(),
                    'data' => $items,
                    'first_page_url' => $featuredNews->url(1),
                    'from' => $featuredNews->firstItem(),
                    'last_page' => $featuredNews->lastPage(),
                    'last_page_url' => $featuredNews->url($featuredNews->lastPage()),
                    'links' => $featuredNews->linkCollection()->toArray(),
                    'next_page_url' => $featuredNews->nextPageUrl(),
                    'path' => $featuredNews->path(),
                    'per_page' => $featuredNews->perPage(),
                    'prev_page_url' => $featuredNews->previousPageUrl(),
                    'to' => $featuredNews->lastItem(),
                    'total' => $featuredNews->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tìm kiếm bài viết
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
            $perPage = min(max((int)$request->input('per_page', 10), 1), 50);

            $news = News::where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('content', 'like', '%' . $query . '%');
                })
                ->select('id', 'title', 'slug', 'avatar', 'content', 'created_at', 'is_featured', 'thumbnail')
                ->latest()
                ->paginate($perPage);
                
            // Chuyển đổi đường dẫn ảnh
            $items = $news->items();
            foreach ($items as $item) {
                $item->avatar = $item->avatar ? asset('storage/' . $item->avatar) : null;
                $item->thumbnail = $item->thumbnail ? asset('storage/' . $item->thumbnail) : null;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'current_page' => $news->currentPage(),
                    'data' => $items,
                    'first_page_url' => $news->url(1),
                    'from' => $news->firstItem(),
                    'last_page' => $news->lastPage(),
                    'last_page_url' => $news->url($news->lastPage()),
                    'links' => $news->linkCollection()->toArray(),
                    'next_page_url' => $news->nextPageUrl(),
                    'path' => $news->path(),
                    'per_page' => $news->perPage(),
                    'prev_page_url' => $news->previousPageUrl(),
                    'to' => $news->lastItem(),
                    'total' => $news->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy bài viết mới nhất 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest(Request $request)
    {
        try {
            $limit = min(max((int)$request->input('limit', 5), 1), 20);

            $latestNews = News::where('is_active', true)
                ->select('id', 'title', 'slug', 'avatar', 'content', 'created_at', 'is_featured', 'thumbnail')
                ->latest()
                ->limit($limit)
                ->get();
                
            // Chuyển đổi đường dẫn ảnh
            foreach ($latestNews as $item) {
                $item->avatar = $item->avatar ? asset('storage/' . $item->avatar) : null;
                $item->thumbnail = $item->thumbnail ? asset('storage/' . $item->thumbnail) : null;
            }

            return response()->json([
                'success' => true,
                'data' => $latestNews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}