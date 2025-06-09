<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\DressStyle;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'categories', 'variants']);

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::all();
        $brands = Brand::all();

        return view('admin.pages.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $dressStyles = DressStyle::all();

        return view('admin.pages.products.create', compact('categories', 'brands', 'dressStyles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description_short' => 'nullable|string|max:500',
            'description_long' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'dress_styles' => 'nullable|array',
            'dress_styles.*' => 'exists:dress_styles,id',
            'avatar' => 'required|image|mimes:jpeg,png,jpg',
            'status' => 'required|in:active,inactive',

            // Validate variants
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:7', // Hex color
            'variants.*.color_name' => 'nullable|string|max:50', // Color display name
            'variants.*.status' => 'required|in:active,inactive', // Status validation
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100|unique:product_variants,sku',

            // Validate product images
            'product_images.*.file' => 'image|mimes:jpeg,png,jpg',
            'product_images.*.color' => 'nullable|string|max:50'
        ], [
            'name.required' => 'Tên sản phẩm là bắt buộc',
            'name.string' => 'Tên sản phẩm phải là chuỗi',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự',
            'description_short.max' => 'Mô tả ngắn không được vượt quá 500 ký tự',
            'description_long.string' => 'Mô tả dài phải là chuỗi',
            'brand_id.exists' => 'Thương hiệu không tồn tại',
            'status.required' => 'Trạng thái sản phẩm là bắt buộc',
            'status.in' => 'Trạng thái sản phẩm không hợp lệ',
            'categories.array' => 'Danh mục phải là một mảng',
            'categories.min' => 'Vui lòng chọn ít nhất một danh mục',
            'categories.*.exists' => 'Danh mục không tồn tại',
            'dress_styles.array' => 'Kiểu dáng phải là một mảng',
            'dress_styles.*.exists' => 'Kiểu dáng không tồn tại',
            'avatar.required' => 'Ảnh đại diện là bắt buộc',
            'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png hoặc jpg',
            'variants.array' => 'Biến thể sản phẩm phải là một mảng',
            'variants.min' => 'Phải có ít nhất một biến thể sản phẩm',
            'variants.*.size.max' => 'Kích thước không được vượt quá 50 ký tự',
            'variants.*.color.max' => 'Màu sắc không được vượt quá 7 ký tự (hex)',
            'variants.*.color_name.max' => 'Tên màu không được vượt quá 50 ký tự',
            'variants.*.status.required' => 'Trạng thái biến thể là bắt buộc',
            'variants.*.status.in' => 'Trạng thái biến thể không hợp lệ',
            'variants.*.price.required' => 'Giá bán là bắt buộc',
            'variants.*.price.numeric' => 'Giá bán phải là một số',
            'variants.*.price.min' => 'Giá bán phải lớn hơn hoặc bằng 0',
            'variants.*.quantity.required' => 'Số lượng là bắt buộc',
            'variants.*.quantity.integer' => 'Số lượng phải là một số nguyên',
            'variants.*.quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 0',
            'variants.*.sku.max' => 'Mã SKU không được vượt quá 100 ký tự',
            'variants.*.sku.unique' => 'Mã SKU đã tồn tại trong hệ thống',
            'product_images.*.file.image' => 'Ảnh sản phẩm phải là một tệp hình ảnh',
            'product_images.*.file.mimes' => 'Ảnh sản phẩm phải có định dạng jpeg, png hoặc jpg',
            'product_images.*.color.max' => 'Màu sắc ảnh không được vượt quá 50 ký tự',
            'categories.required' => 'Vui lòng chọn ít nhất một danh mục',
            'variants.required' => 'Phải có ít nhất một biến thể sản phẩm',
        ]);

        // Validate for single default variant
        $this->validateSingleDefaultVariant($request->variants);

        // Validate for duplicate variants (same color and size)
        $this->validateDuplicateVariants($request->variants);

        // Custom validation for images based on variant color names
        $this->validateVariantImages($request->variants, $request->product_images ?? []);

        DB::beginTransaction();
        try {

            // Process and save avatar
            if ($request->hasFile('avatar')) {
                $avatarPaths = $this->processAndSaveProductImage($request->file('avatar'), 'avatar');
            }

            // Create product
            $product = Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description_short' => $request->description_short,
                'description_long' => $request->description_long,
                'brand_id' => $request->brand_id,
                'status' => $request->status,
                'avatar' => $avatarPaths['original'] ?? null,
                'avatar_medium' => $avatarPaths['medium'] ?? null
            ]);



            // Attach categories and dress styles
            $product->categories()->attach($request->categories);
            if ($request->filled('dress_styles')) {
                $product->dressStyles()->attach($request->dress_styles);
            }

            // Create variants
            foreach ($request->variants as $variantData) {

                $color = !empty($variantData['color']) ? $variantData['color'] : null;
                $colorName = !empty($variantData['color_name']) ? $variantData['color_name'] : null;

                if (empty($color) || empty($colorName)) {
                    $color = null;
                    $colorName = null;
                }

                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variantData['size'],
                    'color' => $color,
                    'color_name' => $colorName,
                    'status' => $variantData['status'],
                    'price' => $variantData['price'],
                    'quantity' => $variantData['quantity'],
                    'sku' => $variantData['sku']
                        ?? Str::slug($product->name . '-' . $variantData['size'] . '-' . $variantData['color_name'])
                        . '-' . Str::random(3)
                ]);
            }

            // Process product images
            if ($request->filled('product_images')) {
                foreach ($request->product_images as $index => $imageData) {
                    if (isset($imageData['file']) && $request->hasFile("product_images.{$index}.file")) {
                        $imagePaths = $this->processAndSaveProductImage($imageData['file'], 'gallery');
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $imagePaths['original'],
                            'image_path_medium' => $imagePaths['medium'],
                            'color' => !empty($imageData['color']) ? trim($imageData['color']) : null
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được tạo thành công',
                'redirect' => route('admin.products.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Product $product)
    {
        $product->load([
            'categories',
            'dressStyles',
            'images',
            'variants'
        ]);
        $categories = Category::all();
        $brands = Brand::all();
        $dressStyles = DressStyle::all();
        $existingProductImages = ProductImage::where('product_id', $product->id)->get();

        return view('admin.pages.products.edit', compact('product', 'categories', 'brands', 'dressStyles', 'existingProductImages'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'dress_styles' => 'nullable|array',
            'dress_styles.*' => 'exists:dress_styles,id',
            'description_short' => 'nullable|string|max:500',
            'description_long' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg',

            // Validate variants
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:7',
            'variants.*.color_name' => 'nullable|string|max:50',
            'variants.*.status' => 'required|in:active,inactive',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100',

            // Validate product images
            'product_images.*.file' => 'nullable|image|mimes:jpeg,png,jpg',
            'product_images.*.color' => 'nullable|string|max:50',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id'
        ], [
            'name.required' => 'Tên sản phẩm là bắt buộc',
            'name.string' => 'Tên sản phẩm phải là chuỗi',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự',
            'description_short.max' => 'Mô tả ngắn không được vượt quá 500 ký tự',
            'description_long.string' => 'Mô tả dài phải là chuỗi',
            'brand_id.exists' => 'Thương hiệu không tồn tại',
            'status.required' => 'Trạng thái sản phẩm là bắt buộc',
            'status.in' => 'Trạng thái sản phẩm không hợp lệ',
            'categories.array' => 'Danh mục phải là một mảng',
            'categories.min' => 'Vui lòng chọn ít nhất một danh mục',
            'categories.*.exists' => 'Danh mục không tồn tại',
            'dress_styles.array' => 'Kiểu dáng phải là một mảng',
            'dress_styles.*.exists' => 'Kiểu dáng không tồn tại',
            'avatar.required' => 'Ảnh đại diện là bắt buộc',
            'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png hoặc jpg',
            'variants.array' => 'Biến thể sản phẩm phải là một mảng',
            'variants.min' => 'Phải có ít nhất một biến thể sản phẩm',
            'variants.*.size.max' => 'Kích thước không được vượt quá 50 ký tự',
            'variants.*.color.max' => 'Màu sắc không được vượt quá 7 ký tự (hex)',
            'variants.*.color_name.max' => 'Tên màu không được vượt quá 50 ký tự',
            'variants.*.status.required' => 'Trạng thái biến thể là bắt buộc',
            'variants.*.status.in' => 'Trạng thái biến thể không hợp lệ',
            'variants.*.price.required' => 'Giá bán là bắt buộc',
            'variants.*.price.numeric' => 'Giá bán phải là một số',
            'variants.*.price.min' => 'Giá bán phải lớn hơn hoặc bằng 0',
            'variants.*.quantity.required' => 'Số lượng là bắt buộc',
            'variants.*.quantity.integer' => 'Số lượng phải là một số nguyên',
            'variants.*.quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 0',
            'variants.*.sku.max' => 'Mã SKU không được vượt quá 100 ký tự',
            'variants.*.sku.unique' => 'Mã SKU đã tồn tại trong hệ thống',
            'product_images.*.file.image' => 'Ảnh sản phẩm phải là một tệp hình ảnh',
            'product_images.*.file.mimes' => 'Ảnh sản phẩm phải có định dạng jpeg, png hoặc jpg',
            'product_images.*.color.max' => 'Màu sắc ảnh không được vượt quá 50 ký tự',
            'categories.required' => 'Vui lòng chọn ít nhất một danh mục',
            'variants.required' => 'Phải có ít nhất một biến thể sản phẩm',
        ]);


        // Validate for single default variant
        $this->validateSingleDefaultVariant($request->variants);

        // Validate for duplicate variants (same color and size)
        $this->validateDuplicateVariants($request->variants);

        // Validate variant images
        $this->validateVariantImages(
            $request->variants,
            $request->product_images ?? [],
            $product,
            $request->delete_images ?? []
        );

        $this->validateProductImages($request, $product);

        // Custom validation for SKU uniqueness (excluding current product variants)
        $existingVariantIds = $product->variants->pluck('id')->toArray();
        foreach ($request->variants as $index => $variantData) {
            if (!empty($variantData['sku'])) {
                $query = ProductVariant::where('sku', $variantData['sku']);
                if (isset($variantData['id']) && in_array($variantData['id'], $existingVariantIds)) {
                    $query->where('id', '!=', $variantData['id']);
                } else {
                    $query->whereNotIn('id', $existingVariantIds);
                }

                if ($query->exists()) {
                    return response()->json([
                        'message' => "Mã SKU '{$variantData['sku']}' đã tồn tại trong hệ thống",
                        'errors' => ["variants.{$index}.sku" => ["Mã SKU đã tồn tại trong hệ thống"]]
                    ], 422);
                }
            }
        }

        DB::beginTransaction();
        try {
            // Process avatar if uploaded
            $avatarPaths = null;
            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($product->avatar) {
                    Storage::delete([$product->avatar, $product->avatar_medium]);
                }

                $avatarPaths = $this->processAndSaveProductImage($request->file('avatar'), 'avatar');
            }

            // Update product
            $updateData = [
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description_short' => $request->description_short,
                'description_long' => $request->description_long,
                'brand_id' => $request->brand_id,
                'status' => $request->status,
            ];

            if ($avatarPaths) {
                $updateData['avatar'] = $avatarPaths['original'];
                $updateData['avatar_medium'] = $avatarPaths['medium'];
            }

            $product->update($updateData);

            // Update categories and dress styles
            $product->categories()->sync($request->categories);
            if ($request->filled('dress_styles')) {
                $product->dressStyles()->sync($request->dress_styles);
            } else {
                $product->dressStyles()->detach();
            }

            // Update variants
            $submittedVariantIds = [];
            foreach ($request->variants as $variantData) {

                $color = !empty($variantData['color']) ? $variantData['color'] : null;
                $colorName = !empty($variantData['color_name']) ? $variantData['color_name'] : null;

                if (empty($color) || empty($colorName)) {
                    $color = null;
                    $colorName = null;
                }

                if (isset($variantData['id'])) {
                    // Update existing variant
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant && $variant->product_id == $product->id) {
                        $variant->update([
                            'size' => $variantData['size'],
                            'color' => $color,
                            'color_name' => $colorName,
                            'status' => $variantData['status'],
                            'price' => $variantData['price'],
                            'quantity' => $variantData['quantity'],
                            'sku' => $variantData['sku'] ?: $variant->sku
                        ]);
                        $submittedVariantIds[] = $variant->id;
                    }
                } else {
                    // Create new variant
                    $newVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $variantData['size'],
                        'color' => $color, // Có thể là null
                        'color_name' => $colorName,
                        'status' => $variantData['status'],
                        'price' => $variantData['price'],
                        'quantity' => $variantData['quantity'],
                        'sku' => $variantData['sku']
                            ?? Str::slug($product->name . '-' . $variantData['size'] . '-' . $variantData['color_name'])
                            . '-' . Str::random(3)
                    ]);
                    $submittedVariantIds[] = $newVariant->id;
                }
            }

            // Delete variants that were removed
            $product->variants()->whereNotIn('id', $submittedVariantIds)->delete();

            // Delete selected existing images
            if ($request->filled('delete_images')) {
                $imagesToDelete = ProductImage::whereIn('id', $request->delete_images)
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    Storage::delete([$image->image_path, $image->image_path_medium]);
                    $image->delete();
                }
            }

            // Process new product images
            if ($request->filled('product_images')) {
                foreach ($request->product_images as $index => $imageData) {
                    if (isset($imageData['file']) && $request->hasFile("product_images.{$index}.file")) {
                        $imagePaths = $this->processAndSaveProductImage($imageData['file'], 'gallery');
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $imagePaths['original'],
                            'image_path_medium' => $imagePaths['medium'],
                            'color' => !empty($imageData['color']) ? trim($imageData['color']) : null
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được cập nhật thành công',
                'redirect' => route('admin.products.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate that there is only one default variant (null color and size)
     */
    private function validateSingleDefaultVariant($variants)
    {
        $defaultVariantCount = 0;

        foreach ($variants as $index => $variantData) {
            $hasSize = !empty($variantData['size']);
            $hasColor = !empty($variantData['color']) && !empty($variantData['color_name']);

            if (!$hasSize && !$hasColor) {
                $defaultVariantCount++;
            }

            if ($defaultVariantCount > 1) {
                throw ValidationException::withMessages([
                    'variants' => ['Chỉ được phép có một biến thể mặc định (không có màu và kích thước)']
                ]);
            }
        }
    }

    /**
     * Validate that there are no duplicate variants (same color and size)
     */
    private function validateDuplicateVariants($variants)
    {
        $variantSignatures = [];

        foreach ($variants as $index => $variantData) {
            $size = $variantData['size'] ?? null;
            $colorName = $variantData['color_name'] ?? null;

            // Create a unique signature for this variant
            $signature = "{$size}_{$colorName}";

            if (in_array($signature, $variantSignatures)) {
                throw ValidationException::withMessages([
                    'variants' => ['Không thể có hai biến thể với cùng màu sắc và kích thước']
                ]);
            }

            $variantSignatures[] = $signature;
        }
    }

    /**
     * Validate proper image coverage for variants
     */
    private function validateVariantImages($variants, $newImages, $product = null, $imagesToDelete = [])
    {
        $hasDefaultVariant = false;
        $hasSizeOnlyVariant = false;
        $colorVariants = [];

        // Analyze variant types
        foreach ($variants as $variant) {
            $hasSize = !empty($variant['size']);
            $hasColor = !empty($variant['color']) && !empty($variant['color_name']);

            if (!$hasSize && !$hasColor) {
                $hasDefaultVariant = true;
            } elseif ($hasSize && !$hasColor) {
                $hasSizeOnlyVariant = true;
            } elseif ($hasColor) {
                $colorVariants[strtolower($variant['color_name'])] = true;
            }
        }

        // Get colors that have images
        $imageColors = [];

        // Check new images
        foreach ($newImages as $image) {
            if (!empty($image['file']) && isset($image['color']) && !empty($image['color'])) {
                $imageColors[strtolower($image['color'])] = true;
            }
        }

        // Check existing images (for update)
        if ($product) {
            $existingImages = ProductImage::where('product_id', $product->id)
                ->whereNotIn('id', $imagesToDelete)
                ->get();

            foreach ($existingImages as $image) {
                if ($image->color) {
                    $imageColors[strtolower($image->color)] = true;
                }
            }
        }

        // Validate that each color variant has an image
        foreach (array_keys($colorVariants) as $colorName) {
            if (!isset($imageColors[$colorName])) {
                throw ValidationException::withMessages([
                    'product_images' => ["Biến thể màu '{$colorName}' cần có ít nhất một ảnh"]
                ]);
            }
        }

        // Check if we need a general image (when there's a default variant or size-only variant)
        if (($hasDefaultVariant || $hasSizeOnlyVariant) && count($newImages) === 0 && !$product) {
            // For new product creation
            throw ValidationException::withMessages([
                'product_images' => ['Cần có ít nhất một ảnh chung khi có biến thể mặc định hoặc biến thể chỉ có kích thước']
            ]);
        }

        if (($hasDefaultVariant || $hasSizeOnlyVariant) && $product) {
            // For product update - verify if there's at least one general image
            $hasGeneralImage = false;

            // Check new images
            foreach ($newImages as $image) {
                if (!isset($image['color']) || empty($image['color'])) {
                    $hasGeneralImage = true;
                    break;
                }
            }

            // Check existing images
            if (!$hasGeneralImage) {
                $hasGeneralImage = ProductImage::where('product_id', $product->id)
                    ->whereNull('color')
                    ->whereNotIn('id', $imagesToDelete)
                    ->exists();
            }

            if (!$hasGeneralImage) {
                throw ValidationException::withMessages([
                    'product_images' => ['Cần có ít nhất một ảnh chung khi có biến thể mặc định hoặc biến thể chỉ có kích thước']
                ]);
            }
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Delete product images
            if ($product->avatar) {
                Storage::disk('public')->delete($product->avatar);
                Storage::disk('public')->delete($product->avatar_medium);
            }

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                Storage::disk('public')->delete($image->image_path_medium);
            }

            $product->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được xóa thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVariantComponent(Request $request)
    {
        $index = $request->get('index', 0);
        $variant = $request->get('variant', []);

        return view('components.variant-item', compact('index', 'variant'))->render();
    }

    public function getImageComponent(Request $request)
    {
        $index = $request->get('index', 0);
        $availableColors = $request->get('availableColors', []);

        return view('components.product-image-item', compact('index', 'availableColors'))->render();
    }

    public function getImageColorOptions(Request $request)
    {
        $index = $request->get('index', 0);
        $colors = $request->get('colors', []);
        $currentSelected = $request->get('currentSelected', '');

        $html = '<div class="color-option no-color-option">
                    <input type="radio" name="product_images[' . $index . '][color]" value="" id="no_color_' . $index . '" ' . (empty($currentSelected) ? 'checked' : '') . '>
                    <label for="no_color_' . $index . '" class="color-label">Ảnh chung (không có màu cụ thể)</label>
                 </div>';

        foreach ($colors as $color) {
            $colorId = 'color_' . $index . '_' . str_replace(' ', '_', strtolower($color));
            $checked = ($currentSelected === $color) ? 'checked' : '';

            $html .= '<div class="color-option">
                        <input type="radio" name="product_images[' . $index . '][color]" value="' . $color . '" id="' . $colorId . '" ' . $checked . '>
                        <label for="' . $colorId . '" class="color-label">' . $color . '</label>
                      </div>';
        }

        return $html;
    }

    private function processAndSaveProductImage($imageFile, $type = 'gallery')
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "product_{$type}_{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("products/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("products/{$yearMonth}/medium");

        $originalImage = Image::make($imageFile);
        if ($originalImage->width() > 800) {
            $originalImage->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        $originalImage->encode('webp', 90);
        $originalPath = "products/{$yearMonth}/original/{$fileName}.webp";
        Storage::disk('public')->put($originalPath, $originalImage->stream());

        $mediumImage = Image::make($imageFile);
        $mediumImage->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $mediumImage->encode('webp', 85);
        $mediumPath = "products/{$yearMonth}/medium/{$fileName}.webp";
        Storage::disk('public')->put($mediumPath, $mediumImage->stream());

        return [
            'original' => $originalPath,
            'medium' => $mediumPath
        ];
    }

    public function getExistingImages(Request $request)
    {
        $productId = $request->product_id;

        if (!$productId) {
            return response()->json(['colors' => []]);
        }

        $colors = ProductImage::where('product_id', $productId)
            ->select('color')
            ->distinct()
            ->pluck('color')
            ->toArray();

        return response()->json(['colors' => $colors]);
    }

    private function validateProductImages(Request $request, Product $product)
    {
        // Get images that will be deleted
        $imagesToDelete = $request->delete_images ?? [];

        // Count remaining existing images
        $remainingImagesCount = ProductImage::where('product_id', $product->id)
            ->whereNotIn('id', $imagesToDelete)
            ->count();

        // Count new images
        $newImagesCount = $request->product_images ? count($request->product_images) : 0;

        $totalImages = $remainingImagesCount + $newImagesCount;

        // Must have at least 1 image
        if ($totalImages === 0) {
            throw ValidationException::withMessages([
                'product_images' => ['Sản phẩm phải có ít nhất 1 ảnh']
            ]);
        }

        // Check for general images (color = null)
        $hasGeneralImage = ProductImage::where('product_id', $product->id)
            ->whereNull('color')
            ->whereNotIn('id', $imagesToDelete)
            ->exists();

        // Check new images for general image
        if (!$hasGeneralImage && $request->product_images) {
            foreach ($request->product_images as $imageData) {
                if (!isset($imageData['color']) || empty($imageData['color'])) {
                    $hasGeneralImage = true;
                    break;
                }
            }
        }

        if (!$hasGeneralImage) {
            throw ValidationException::withMessages([
                'product_images' => ['Phải có ít nhất 1 ảnh chung (không gắn với màu cụ thể nào)']
            ]);
        }
    }
}
