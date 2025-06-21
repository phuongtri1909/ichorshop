<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\CategoryBlog;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Hiển thị danh sách bài viết
     */
    public function index(Request $request)
    {
        // Lấy bài viết với sắp xếp và lọc
        $blogsQuery = Blog::with(['author', 'categories'])
            ->where('is_active', true);
            
        // Lọc theo từ khóa tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $keyword = $request->search;
            $blogsQuery->where('title', 'like', "%{$keyword}%")
                ->orWhere('content', 'like', "%{$keyword}%");
        }

        // Lọc theo danh mục (nếu có)
        if ($request->has('category') && !empty($request->category)) {
            $blogsQuery->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Phân trang kết quả
        $blogs = $blogsQuery->latest()->paginate(20);

        
        // Lấy danh mục và bài viết mới nhất cho sidebar
        $categories = CategoryBlog::withCount('blogs')->orderBy('name')->get();
        $latestPosts = Blog::where('is_active', true)
            ->latest()
            ->take(5)
            ->get();

        return view('client.pages.blogs.index', compact('blogs', 'categories', 'latestPosts'));
    }

    /**
     * Hiển thị chi tiết bài viết
     */
    public function show($slug)
    {
        $blog = Blog::with(['author', 'categories'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Tăng lượt xem
        $blog->incrementViews();

        // Lấy bài viết liên quan
        $relatedPosts = Blog::with(['categories'])
            ->where('id', '!=', $blog->id)
            ->where('is_active', true)
            ->whereHas('categories', function ($query) use ($blog) {
                $query->whereIn('category_blogs.id', $blog->categories->pluck('id'));
            })
            ->latest()
            ->take(2)
            ->get();
            
        
        // Lấy danh mục và bài viết mới nhất cho sidebar
        $categories = CategoryBlog::withCount('blogs')->orderBy('name')->get();
        $latestPosts = Blog::where('is_active', true)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(5)
            ->get();

        return view('client.pages.blogs.show', compact(
            'blog', 
            'relatedPosts', 
            'categories', 
            'latestPosts'
        ));
    }
    
    /**
     * Hiển thị danh sách bài viết theo danh mục
     */
    public function category($slug)
    {
        $category = CategoryBlog::where('slug', $slug)->firstOrFail();
        
        $blogs = Blog::with(['author', 'categories'])
            ->where('is_active', true)
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('category_blogs.id', $category->id);
            })
            ->latest()
            ->paginate(6);
        
        // Lấy danh mục và bài viết mới nhất cho sidebar
        $categories = CategoryBlog::withCount('blogs')->orderBy('name')->get();
        $latestPosts = Blog::where('is_active', true)
            ->latest()
            ->take(5)
            ->get();

        return view('client.pages.blogs.index', compact('blogs', 'categories', 'latestPosts', 'category'));
    }
    
    /**
     * Lưu bình luận cho bài viết
     */
    public function storeComment(Request $request, $slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $comment = new BlogComment();
        $comment->blog_id = $blog->id;
        $comment->user_id = auth()->id();
        $comment->content = $validated['content'];
        $comment->is_approved = true; // Có thể bạn muốn thay đổi thành false để kiểm duyệt trước
        $comment->save();
        
        return redirect()->back()->with('success', 'Your comment has been posted successfully!');
    }
}
