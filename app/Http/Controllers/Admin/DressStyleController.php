<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\DressStyle;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class DressStyleController extends Controller
{
    public function index()
    {
        $dressStyles = DressStyle::latest()->paginate(20);
        return view('admin.pages.dress-styles.index', compact('dressStyles'));
    }

    public function create()
    {
        return view('admin.pages.dress-styles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:dress_styles,name',
            'description' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg'
        ], [
            'name.required' => 'Tên kiểu dáng là bắt buộc',
            'name.unique' => 'Tên kiểu dáng đã tồn tại',
            'banner.image' => 'Banner phải là ảnh',
            'banner.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ];

        if ($request->hasFile('banner')) {
            $bannerPath = $this->processAndSaveBanner($request->file('banner'));
            $data['banner'] = $bannerPath;
        }

        DressStyle::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kiểu dáng đã được tạo thành công',
            'redirect' => route('admin.dress-styles.index')
        ]);
    }

    public function edit(DressStyle $dressStyle)
    {
        return view('admin.pages.dress-styles.edit', compact('dressStyle'));
    }

    public function update(Request $request, DressStyle $dressStyle)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:dress_styles,name,' . $dressStyle->id,
            'description' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg'
        ], [
            'name.required' => 'Tên kiểu dáng là bắt buộc',
            'name.unique' => 'Tên kiểu dáng đã tồn tại',
            'banner.image' => 'Banner phải là ảnh',
            'banner.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ];

        if ($request->hasFile('banner')) {
            // Delete old banner
            if ($dressStyle->banner) {
                Storage::disk('public')->delete($dressStyle->banner);
            }

            $bannerPath = $this->processAndSaveBanner($request->file('banner'));
            $data['banner'] = $bannerPath;
        }

        $dressStyle->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kiểu dáng đã được cập nhật thành công',
            'redirect' => route('admin.dress-styles.index')
        ]);
    }

    public function destroy(DressStyle $dressStyle)
    {
        if ($dressStyle->banner) {
            Storage::disk('public')->delete($dressStyle->banner);
        }

        $dressStyle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kiểu dáng đã được xóa thành công'
        ]);
    }

    private function processAndSaveBanner($bannerFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "dress_style_{$timestamp}_{$randomString}";

        // Create directory if it doesn't exist
        Storage::disk('public')->makeDirectory("dress_styles/banners/{$yearMonth}");

        // Process and compress image
        $image = Image::make($bannerFile);

        // Resize if too large (max width: 1200px, maintain aspect ratio)
        // Banner thường có kích thước lớn hơn logo
        if ($image->width() > 1200) {
            $image->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Convert to WebP format for better compression
        $image->encode('webp', 85); // 85% quality

        // Save the processed image
        $bannerPath = "dress_styles/banners/{$yearMonth}/{$fileName}.webp";
        Storage::disk('public')->put($bannerPath, $image->stream());

        return $bannerPath;
    }
}
