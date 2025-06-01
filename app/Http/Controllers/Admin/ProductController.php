<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Helpers\ImageHelper;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Lấy danh sách tất cả danh mục cho bộ lọc
        $categories = Category::orderBy('sort_order')->get();

        // Xây dựng query với các filter
        $productsQuery = Product::with(['category', 'images', 'weights']);

        // Filter theo danh mục
        if ($request->has('category_id') && !empty($request->category_id)) {
            $productsQuery->where('category_id', $request->category_id);
        }

        // Filter theo tên sản phẩm
        if ($request->has('name') && !empty($request->name)) {
            $productsQuery->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter theo trạng thái
        if ($request->has('status') && !empty($request->status)) {
            $isActive = ($request->status === 'active');
            $productsQuery->where('is_active', $isActive);
        }

        // Sắp xếp theo ngày tạo mới nhất
        $productsQuery->orderBy('created_at', 'desc');

        // Thực thi query và phân trang kết quả
        $products = $productsQuery->paginate(10);

        return view('admin.pages.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.pages.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:2000',
            'highlight' => 'nullable|array',
            'highlight.*' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'weights' => 'required|array|min:1',
            'weights.*.weight' => 'required|string|max:50',
            'weights.*.sku' => 'required|string|max:50|unique:product_weights',
            'weights.*.original_price' => 'required|numeric|min:0',
            'weights.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'weights.*.is_default' => 'boolean',
            'weights.*.is_active' => 'boolean',
            'additional_images' => 'nullable|array',
            'additional_images.*' => 'image|mimes:jpeg,png,jpg',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống',
            'name.unique' => 'Tên sản phẩm đã tồn tại',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'description.required' => 'Mô tả sản phẩm không được để trống',
            'description.max' => 'Mô tả sản phẩm không được vượt quá 2000 ký tự',
            'image.required' => 'Hình ảnh sản phẩm không được để trống',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png hoặc jpg',
            'weights.required' => 'Sản phẩm cần có ít nhất một quy cách',
            'weights.min' => 'Sản phẩm cần có ít nhất một quy cách',
            'weights.*.weight.required' => 'Quy cách không được để trống',
            'weights.*.sku.required' => 'Mã sản phẩm không được để trống',
            'weights.*.sku.unique' => 'Mã sản phẩm đã tồn tại',
            'weights.*.sku.max' => 'Mã sản phẩm không được vượt quá 50 ký tự',
            'weights.*.original_price.required' => 'Giá không được để trống',
            'weights.*.original_price.numeric' => 'Giá phải là số',
            'weights.*.original_price.min' => 'Giá không được nhỏ hơn 0',
            'weights.*.discount_percent.numeric' => 'Phần trăm giảm giá phải là số',
            'weights.*.discount_percent.min' => 'Phần trăm giảm giá không được nhỏ hơn 0',
            'weights.*.discount_percent.max' => 'Phần trăm giảm giá không được lớn hơn 100',
            'additional_images.*.image' => 'File phải là hình ảnh',
            'additional_images.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png hoặc jpg',
        ]);

        try {
            DB::beginTransaction();

            // Clean empty highlight items
            if (isset($validated['highlight'])) {
                $validated['highlight'] = array_filter($validated['highlight']);
            } else {
                $validated['highlight'] = [];
            }

            // Generate slug from name
            $validated['slug'] = Str::slug($validated['name']);

            // Tạm thời lưu đường dẫn ảnh để đảm bảo có thể xóa nếu xảy ra lỗi
            $imagePaths = [];

            // Xử lý và lưu ảnh chính
            if ($request->hasFile('image')) {
                $imagePaths['main'] = ImageHelper::optimizeAndSave($request->file('image'), 'images/products');
                $validated['image'] = $imagePaths['main'];
            }

            // Mặc định is_featured và is_active nếu không có
            $validated['is_featured'] = $request->has('is_featured');
            $validated['is_active'] = $request->has('is_active');

            // Tạo product
            $product = Product::create($validated);

            // Lưu quy cách
            foreach ($validated['weights'] as $weight) {
                $weight['is_default'] = isset($weight['is_default']) ? true : false;
                $weight['is_active'] = isset($weight['is_active']) ? true : false;

                $discountPercent = $weight['discount_percent'] ?? 0;
                $originalPrice = $weight['original_price'];
                $weight['discounted_price'] = $originalPrice - ($originalPrice * ($discountPercent / 100));
                $product->weights()->create($weight);
            }

            // Xử lý và lưu ảnh bổ sung
            if ($request->hasFile('additional_images')) {
                $sortOrder = 1;
                foreach ($request->file('additional_images') as $index => $image) {
                    $additionalImagePath = ImageHelper::optimizeAndSave($image, 'images/products', 800);
                    $imagePaths['additional_' . $index] = $additionalImagePath;

                    $product->images()->create([
                        'image_path' => $additionalImagePath,
                        'sort_order' => $sortOrder++
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được tạo thành công.',
                    'redirect' => route('admin.products.index')
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollback();

            // Xóa các ảnh đã lưu nếu có lỗi xảy ra
            foreach ($imagePaths as $path) {
                ImageHelper::delete($path);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã xảy ra lỗi khi tạo sản phẩm: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Đã xảy ra lỗi khi tạo sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->get();
        $product->load(['category', 'weights', 'images']);
        return view('admin.pages.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id),
            ],
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:2000',
            'highlight' => 'nullable|array',
            'highlight.*' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'weights' => 'required|array|min:1',
            'weights.*.id' => 'nullable|exists:product_weights,id',
            'weights.*.weight' => 'required|string|max:50',
            'weights.*.original_price' => 'required|numeric|min:0',
            'weights.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'weights.*.is_default' => 'boolean',
            'weights.*.is_active' => 'boolean',
            'additional_images' => 'nullable|array',
            'additional_images.*' => 'image|mimes:jpeg,png,jpg',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
        ];

        // Tạo rules động cho SKU của mỗi weight
        if ($request->has('weights')) {
            foreach ($request->weights as $key => $weight) {
                if (isset($weight['id'])) {
                    // Nếu có ID (đang cập nhật weight hiện có), sử dụng ID của weight để ignore
                    $rules['weights.' . $key . '.sku'] = [
                        'required',
                        'string',
                        'max:50',
                        Rule::unique('product_weights', 'sku')->ignore($weight['id'])
                    ];
                } else {
                    // Nếu không có ID (thêm weight mới), kiểm tra unique toàn bộ
                    $rules['weights.' . $key . '.sku'] = [
                        'required',
                        'string',
                        'max:50',
                        'unique:product_weights,sku'
                    ];
                }
            }
        }

        // Messages
        $messages = [
            'name.required' => 'Tên sản phẩm không được để trống',
            'name.unique' => 'Tên sản phẩm đã tồn tại',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'description.required' => 'Mô tả sản phẩm không được để trống',
            'description.max' => 'Mô tả sản phẩm không được vượt quá 2000 ký tự',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png hoặc jpg',
            'weights.required' => 'Sản phẩm cần có ít nhất một quy cách',
            'weights.min' => 'Sản phẩm cần có ít nhất một quy cách',
            'weights.*.weight.required' => 'Quy cách không được để trống',
            'weights.*.sku.required' => 'Mã sản phẩm không được để trống',
            'weights.*.sku.unique' => 'Mã sản phẩm đã tồn tại',
            'weights.*.sku.max' => 'Mã sản phẩm không được vượt quá 50 ký tự',
            'weights.*.original_price.required' => 'Giá không được để trống',
            'weights.*.original_price.numeric' => 'Giá phải là số',
            'weights.*.original_price.min' => 'Giá không được nhỏ hơn 0',
            'weights.*.discount_percent.numeric' => 'Phần trăm giảm giá phải là số',
            'weights.*.discount_percent.min' => 'Phần trăm giảm giá không được nhỏ hơn 0',
            'weights.*.discount_percent.max' => 'Phần trăm giảm giá không được lớn hơn 100',
            'additional_images.*.image' => 'File phải là hình ảnh',
            'additional_images.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png hoặc jpg',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // Clean empty highlight items
            if (isset($validated['highlight'])) {
                // Filter out empty values
                $validated['highlight'] = array_values(array_filter($validated['highlight'], function($item) {
                    return !empty(trim($item));
                }));
                
                // Remove duplicates (case insensitive)
                $uniqueHighlights = [];
                $lowercaseHighlights = [];
                
                foreach ($validated['highlight'] as $item) {
                    $lowercaseItem = strtolower($item);
                    if (!in_array($lowercaseItem, $lowercaseHighlights)) {
                        $lowercaseHighlights[] = $lowercaseItem;
                        $uniqueHighlights[] = $item;
                    }
                }
                
                $validated['highlight'] = $uniqueHighlights;
            } else {
                $validated['highlight'] = [];
            }

            // Generate slug from name if name has changed
            if ($product->name !== $validated['name']) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Mảng lưu trữ đường dẫn ảnh mới để có thể xóa nếu xảy ra lỗi
            $newImagePaths = [];
            // Lưu ảnh cũ để xóa sau khi cập nhật thành công
            $oldMainImage = $product->image;

            // Xử lý và lưu ảnh chính nếu có
            if ($request->hasFile('image')) {
                $newImagePaths['main'] = ImageHelper::optimizeAndSave($request->file('image'), 'images/products', 800);
                $validated['image'] = $newImagePaths['main'];
            }

            // Set boolean fields
            $validated['is_featured'] = $request->has('is_featured');
            $validated['is_active'] = $request->has('is_active');

            // Cập nhật product
            $product->update($validated);

            // Xử lý các quy cách
            $existingWeightIds = $product->weights->pluck('id')->toArray();
            $newWeightIds = [];

            foreach ($validated['weights'] as $weightData) {
                $weightData['is_default'] = isset($weightData['is_default']) ? true : false;
                $weightData['is_active'] = isset($weightData['is_active']) ? true : false;

                $discountPercent = $weightData['discount_percent'] ?? 0;
                $originalPrice = $weightData['original_price'];
                $weightData['discounted_price'] = $originalPrice - ($originalPrice * ($discountPercent / 100));

                if (isset($weightData['id'])) {
                    // Cập nhật quy cách hiện có
                    $weight = ProductWeight::find($weightData['id']);
                    $weight->update($weightData);
                    $newWeightIds[] = $weight->id;
                } else {
                    // Tạo quy cách mới
                    $weight = $product->weights()->create($weightData);
                    $newWeightIds[] = $weight->id;
                }
            }

            // Xóa các quy cách không còn tồn tại
            $weightsToDelete = array_diff($existingWeightIds, $newWeightIds);
            if (!empty($weightsToDelete)) {
                ProductWeight::whereIn('id', $weightsToDelete)->delete();
            }

            // Lưu danh sách ảnh cần xóa để xóa sau khi cập nhật thành công
            $imagesToDeletePaths = [];

            // Lưu thông tin ảnh bị đánh dấu xóa
            if (isset($validated['delete_images']) && !empty($validated['delete_images'])) {
                $imagesToDelete = ProductImage::whereIn('id', $validated['delete_images'])->get();
                foreach ($imagesToDelete as $image) {
                    $imagesToDeletePaths[] = $image->image_path;
                }

                // Xóa các bản ghi ảnh (không xóa file ảnh ngay)
                ProductImage::whereIn('id', $validated['delete_images'])->delete();
            }

            // Thêm ảnh mới nếu có
            if ($request->hasFile('additional_images')) {
                $maxSortOrder = $product->images()->max('sort_order') ?? 0;
                foreach ($request->file('additional_images') as $index => $image) {
                    $additionalImagePath = ImageHelper::optimizeAndSave($image, 'images/products', 800);
                    $newImagePaths['additional_' . $index] = $additionalImagePath;

                    $product->images()->create([
                        'image_path' => $additionalImagePath,
                        'sort_order' => ++$maxSortOrder
                    ]);
                }
            }

            DB::commit();

            // Xóa các ảnh cũ sau khi cập nhật thành công
            if ($request->hasFile('image') && $oldMainImage) {
                ImageHelper::delete($oldMainImage);
            }

            // Xóa các ảnh bổ sung đã đánh dấu xóa
            foreach ($imagesToDeletePaths as $path) {
                ImageHelper::delete($path);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được cập nhật thành công.',
                    'redirect' => route('admin.products.index')
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollback();

            // Xóa các ảnh mới đã lưu nếu có lỗi xảy ra
            foreach ($newImagePaths as $path) {
                ImageHelper::delete($path);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã xảy ra lỗi khi cập nhật sản phẩm: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Đã xảy ra lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Lưu danh sách ảnh để xóa sau khi toàn bộ quá trình xóa hoàn tất
            $imagesToDelete = [];

            // Thêm ảnh chính vào danh sách cần xóa
            if ($product->image) {
                $imagesToDelete[] = $product->image;
            }

            // Thêm các ảnh bổ sung vào danh sách cần xóa
            foreach ($product->images as $image) {
                $imagesToDelete[] = $image->image_path;
            }

            // Xóa các bản ghi liên quan
            $product->weights()->delete();
            $product->images()->delete();

            // Xóa sản phẩm
            $product->delete();

            DB::commit();

            // Xóa các file ảnh sau khi đã xóa dữ liệu thành công
            foreach ($imagesToDelete as $imagePath) {
                ImageHelper::delete($imagePath);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi xóa sản phẩm: ' . $e->getMessage());
        }
    }
}
