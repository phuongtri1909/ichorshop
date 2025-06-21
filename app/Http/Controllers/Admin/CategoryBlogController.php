<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\CategoryBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryBlogController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = CategoryBlog::withCount('blogs')
            ->orderBy('name')
            ->paginate(10);
        
        return view('admin.pages.category-blogs.index', compact('categories'));
    }
    
    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.pages.category-blogs.create');
    }
    
    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:category_blogs',
            'description' => 'nullable|string'
        ], [
            'name.required' => 'Tên danh mục không được để trống',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự'
        ]);
        
        // Create slug from name
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;
        
        // Ensure slug is unique
        while (CategoryBlog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        // Create category
        CategoryBlog::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được tạo thành công!',
                'redirect' => route('admin.category-blogs.index')
            ]);
        }
        
        return redirect()->route('admin.category-blogs.index')
            ->with('success', 'Danh mục đã được tạo thành công!');
    }
    
    /**
     * Show the form for editing a category
     */
    public function edit(CategoryBlog $categoryBlog)
    {
        $categoryBlog->loadCount('blogs');
        return view('admin.pages.category-blogs.edit', ['category' => $categoryBlog]);
    }
    
    /**
     * Update a category
     */
    public function update(Request $request, CategoryBlog $categoryBlog)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:category_blogs,name,' . $categoryBlog->id,
            'description' => 'nullable|string'
        ], [
            'name.required' => 'Tên danh mục không được để trống',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự'
        ]);
        
        // Check if name has changed
        if ($request->name !== $categoryBlog->name) {
            // Create a new slug
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $count = 1;
            
            // Ensure slug is unique
            while (CategoryBlog::where('slug', $slug)->where('id', '!=', $categoryBlog->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            
            $categoryBlog->slug = $slug;
        }
        
        // Update category
        $categoryBlog->name = $request->name;
        $categoryBlog->description = $request->description;
        $categoryBlog->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được cập nhật thành công!',
                'redirect' => route('admin.category-blogs.index')
            ]);
        }
        
        return redirect()->route('admin.category-blogs.index')
            ->with('success', 'Danh mục đã được cập nhật thành công!');
    }
    
    /**
     * Delete a category
     */
    public function destroy(CategoryBlog $categoryBlog)
    {
        // Detach all blogs
        $categoryBlog->blogs()->detach();
        
        // Delete the category
        $categoryBlog->delete();
        
        return redirect()->route('admin.category-blogs.index')
            ->with('success', 'Danh mục đã được xóa thành công!');
    }
}
