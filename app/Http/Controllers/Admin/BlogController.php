<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Blog;
use App\Models\CategoryBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Hiển thị danh sách bài viết
     */
    public function index(Request $request)
    {
        $query = Blog::with(['author', 'categories']);

        // Filter theo danh mục
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_blogs.id', $request->category_id);
            });
        }

        // Filter theo tiêu đề
        if ($request->has('title') && !empty($request->title)) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Filter theo trạng thái
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status == 'active') {
                $query->where('is_active', true);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status == 'featured') {
                $query->where('is_featured', true);
            }
        }

        // Sắp xếp
        $query->orderBy('created_at', 'desc');

        // Lấy danh sách bài viết
        $blogs = $query->paginate(10);
        
        // Lấy danh sách danh mục cho bộ lọc
        $categories = CategoryBlog::orderBy('name')->get();

        return view('admin.pages.blogs.index', compact('blogs', 'categories'));
    }

    /**
     * Hiển thị form tạo bài viết mới
     */
    public function create()
    {
        $categories = CategoryBlog::orderBy('name')->get();
        return view('admin.pages.blogs.create', compact('categories'));
    }

    /**
     * Lưu bài viết mới vào database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:category_blogs,id',
            'avatar' => 'required|image|max:2048', // max 2MB
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ],[
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'content.required' => 'Nội dung là bắt buộc.',
            'categories.required' => 'Vui lòng chọn ít nhất một danh mục.',
            'categories.array' => 'Danh mục phải là một mảng.',
            'categories.*.exists' => 'Danh mục không hợp lệ.',
            'avatar.required' => 'Hình ảnh đại diện là bắt buộc.',
            'avatar.image' => 'Hình ảnh đại diện phải là một tệp hình ảnh hợp lệ.',
            'avatar.max' => 'Kích thước hình ảnh đại diện không được vượt quá 2MB.',
            'is_active.boolean' => 'Trạng thái hiển thị phải là đúng hoặc sai.',
            'is_featured.boolean' => 'Trạng thái nổi bật phải là đúng hoặc sai.'
        ]);

        // Xử lý slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;
        
        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Xử lý hình ảnh
        $imagePath = null;
        if ($request->hasFile('avatar')) {
            $imagePath = $request->file('avatar')->store('blogs', 'public');
        }

        // Tạo bài viết mới
        $blog = Blog::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'image' => $imagePath,
            'user_id' => Auth::id(),
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'author_id' => Auth::id(),
        ]);

        // Gán danh mục
        $blog->categories()->sync($validated['categories']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bài viết đã được tạo thành công!',
                'redirect' => route('admin.blogs.index')
            ]);
        }

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Bài viết đã được tạo thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa bài viết
     */
    public function edit(Blog $blog)
    {
        $categories = CategoryBlog::orderBy('name')->get();
        $selectedCategories = $blog->categories->pluck('id')->toArray();
        
        return view('admin.pages.blogs.edit', compact('blog', 'categories', 'selectedCategories'));
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:category_blogs,id',
            'avatar' => 'sometimes|nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'remove_avatar' => 'sometimes|boolean',
        ],[
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'content.required' => 'Nội dung là bắt buộc.',
            'categories.required' => 'Vui lòng chọn ít nhất một danh mục.',
            'categories.array' => 'Danh mục phải là một mảng.',
            'categories.*.exists' => 'Danh mục không hợp lệ.',
            'avatar.image' => 'Hình ảnh đại diện phải là một tệp hình ảnh hợp lệ.',
            'avatar.max' => 'Kích thước hình ảnh đại diện không được vượt quá 2MB.',
            'is_active.boolean' => 'Trạng thái hiển thị phải là đúng hoặc sai.',
            'is_featured.boolean' => 'Trạng thái nổi bật phải là đúng hoặc sai.'
        ]);

        // Xử lý hình ảnh
        $imagePath = $blog->image;
        
        if ($request->has('remove_avatar') && $request->remove_avatar == 1) {
            // Xóa hình ảnh cũ nếu có
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $imagePath = null;
        }
        
        if ($request->hasFile('avatar')) {
            // Xóa hình ảnh cũ nếu có
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            
            $imagePath = $request->file('avatar')->store('blogs', 'public');
        }

        // Cập nhật bài viết
        $blog->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'author_id' => Auth::id(),
        ]);

        // Cập nhật danh mục
        $blog->categories()->sync($validated['categories']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bài viết đã được cập nhật thành công!',
                'redirect' => route('admin.blogs.index')
            ]);
        }

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    /**
     * Xóa bài viết
     */
    public function destroy(Blog $blog)
    {
        // Xóa hình ảnh nếu có
        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }
        
        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Bài viết đã được xóa thành công!');
    }
    
    /**
     * Upload hình ảnh từ CKEditor
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $fileName = $request->file('upload')->store('blogs/content', 'public');
            $url = asset('storage/' . $fileName);
            
            return response()->json([
                'uploaded' => 1,
                'fileName' => basename($fileName),
                'url' => $url
            ]);
        }
        
        return response()->json([
            'uploaded' => 0,
            'error' => [
                'message' => 'Không thể tải lên hình ảnh.'
            ]
        ]);
    }
}