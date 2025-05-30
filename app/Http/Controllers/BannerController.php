<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::orderBy('sort_order', 'asc')
            ->paginate(10);
            
        return view('admin.pages.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);
        
        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                // Lưu ảnh và tối ưu hóa
                $imagePath = ImageHelper::optimizeAndSave(
                    $request->file('image'),
                    'banners',
                    1200 // width tối đa để đảm bảo chất lượng ảnh banner
                );
            }
            
            $validated['image'] = $imagePath;
            $validated['is_active'] = $request->has('is_active');
            
            Banner::create($validated);
            
            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner đã được tạo thành công.');
        } catch (\Exception $e) {
            // Xóa ảnh nếu có lỗi xảy ra
            if ($imagePath) {
                ImageHelper::delete($imagePath);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('admin.pages.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);
        
        try {
            // Xử lý và lưu ảnh mới nếu có
            if ($request->hasFile('image')) {
                $oldImagePath = $banner->image;
                
                // Lưu ảnh mới và tối ưu hóa
                $imagePath = ImageHelper::optimizeAndSave(
                    $request->file('image'),
                    'banners',
                    1200 // width tối đa để đảm bảo chất lượng ảnh banner
                );
                
                $validated['image'] = $imagePath;
            }
            
            $validated['is_active'] = $request->has('is_active');
            
            $banner->update($validated);
            
            // Xóa ảnh cũ nếu đã cập nhật ảnh mới
            if (isset($oldImagePath)) {
                ImageHelper::delete($oldImagePath);
            }
            
            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner đã được cập nhật thành công.');
        } catch (\Exception $e) {
            // Xóa ảnh mới nếu có lỗi xảy ra
            if (isset($imagePath)) {
                ImageHelper::delete($imagePath);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        try {
            $oldImagePath = $banner->image;
            
            // Xóa banner
            $banner->delete();
            
            // Xóa ảnh
            if ($oldImagePath) {
                ImageHelper::delete($oldImagePath);
            }
            
            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner đã được xóa thành công.');
        } catch (\Exception $e) {
            return redirect()->route('admin.banners.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
