<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(20);
        return view('admin.pages.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.pages.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg'
        ], [
            'name.required' => 'Tên thương hiệu là bắt buộc',
            'name.unique' => 'Tên thương hiệu đã tồn tại',
            'logo.image' => 'Logo phải là ảnh',
            'logo.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ];

        if ($request->hasFile('logo')) {
            $logoPath = $this->processAndSaveLogo($request->file('logo'));
            $data['logo'] = $logoPath;
        }

        Brand::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thương hiệu đã được tạo thành công',
            'redirect' => route('admin.brands.index')
        ]);
    }

    public function edit(Brand $brand)
    {
        return view('admin.pages.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg'
        ], [
            'name.required' => 'Tên thương hiệu là bắt buộc',
            'name.unique' => 'Tên thương hiệu đã tồn tại',
            'logo.image' => 'Logo phải là ảnh',
            'logo.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ];

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            
            $logoPath = $this->processAndSaveLogo($request->file('logo'));
            $data['logo'] = $logoPath;
        }

        $brand->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Thương hiệu đã được cập nhật thành công',
            'redirect' => route('admin.brands.index')
        ]);
    }

    public function destroy(Brand $brand)
    {
        // Delete logo file if exists
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }
        
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Thương hiệu đã được xóa thành công');
    }

    /**
     * Process and save brand logo with compression
     */
    private function processAndSaveLogo($logoFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "brand_{$timestamp}_{$randomString}";

        // Create directory if it doesn't exist
        Storage::disk('public')->makeDirectory("brands/logos/{$yearMonth}");

        // Process and compress image
        $image = Image::make($logoFile);
        
        // Resize if too large (max width: 400px, maintain aspect ratio)
        if ($image->width() > 400) {
            $image->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Convert to WebP format for better compression
        $image->encode('webp', 85); // 85% quality

        // Save the processed image
        $logoPath = "brands/logos/{$yearMonth}/{$fileName}.webp";
        Storage::disk('public')->put($logoPath, $image->stream());

        return $logoPath;
    }
}