<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product']);

        // Filter by product if provided
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by color name
        if ($request->filled('color_name')) {
            $query->where('color_name', 'like', '%' . $request->color_name . '%');
        }

        // Filter by size
        if ($request->filled('size')) {
            $query->where('size', 'like', '%' . $request->size . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $productVariants = $query->latest()->paginate(15);
        $products = Product::all();

        // Get selected product if filtering by product_id
        $selectedProduct = null;
        if ($request->filled('product_id')) {
            $selectedProduct = Product::find($request->product_id);
        }

        return view('admin.pages.product-variants.index', compact('productVariants', 'products', 'selectedProduct'));
    }

    public function create(Request $request)
    {
        $products = Product::all();
        $selectedProduct = null;
        $productImages = collect();
        $existingProductColors = [];
        
        if ($request->filled('product_id')) {
            $selectedProduct = Product::find($request->product_id);
            
            // Get all product images (general + all colors) for preview
            $productImages = ProductImage::where('product_id', $selectedProduct->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Get existing colors
            $existingProductColors = $productImages->pluck('color')
                ->filter()
                ->unique()
                ->toArray();
        }
        
        return view('admin.pages.product-variants.create', compact(
            'products', 
            'selectedProduct', 
            'productImages',
            'existingProductColors'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'color_name' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku',
            'variant_images' => 'nullable|array',
            'variant_images.*.file' => 'required_with:variant_images|image|mimes:jpeg,png,jpg|max:2048',
            'variant_images.*.color' => 'nullable|string|max:50'
        ]);

        // Enhanced validation for images
        if ($request->color_name) {
            $hasRequiredImages = $this->checkColorImageRequirement(
                $request->product_id, 
                $request->color_name, 
                $request->variant_images ?? []
            );
            
            if (!$hasRequiredImages) {
                return response()->json([
                    'success' => false,
                    'message' => 'Màu "' . $request->color_name . '" chưa có ảnh nào. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.',
                    'errors' => ['variant_images' => ['Màu "' . $request->color_name . '" chưa có ảnh nào. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.']]
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Create variant logic...
            $variant = ProductVariant::create([
                'product_id' => $request->product_id,
                'size' => $request->size,
                'color' => $request->color,
                'color_name' => $request->color_name,
                'status' => $request->status,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'sku' => $request->sku ?: $this->generateSKU($request)
            ]);

            // Process variant images
            if ($request->variant_images) {
                foreach ($request->variant_images as $imageData) {
                    if (isset($imageData['file'])) {
                        $imagePaths = $this->processAndSaveProductImage($imageData['file'], 'variant');
                        
                        ProductImage::create([
                            'product_id' => $variant->product_id,
                            'image_path' => $imagePaths['original'],
                            'image_path_medium' => $imagePaths['medium'],
                            'color' => $imageData['color'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biến thể sản phẩm đã được tạo thành công',
                'redirect' => route('admin.product-variants.index', ['product_id' => $variant->product_id])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating product variant: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(ProductVariant $productVariant)
    {
        $products = Product::all();
        $selectedProduct = null;
        $productImages = collect();
        $existingProductColors = [];

        if ($productVariant->product_id) {
            $selectedProduct = Product::find($productVariant->product_id);

            // Get all product images (general + all colors) for preview
            $existingProductImages = ProductImage::where('product_id', $selectedProduct->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $variantImages = $existingProductImages->where('color', $productVariant->color)
                ->values();
                
            // Get existing colors
            $existingProductColors = $existingProductImages->pluck('color')
                ->filter()
                ->unique()
                ->toArray();
        }

        return view('admin.pages.product-variants.edit', compact(
            'productVariant', 
            'products', 
            'existingProductColors', 
            'existingProductImages',
            'variantImages'
        ));
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'color_name' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku,' . $productVariant->id,
            'variant_images' => 'nullable|array',
            'variant_images.*.file' => 'required_with:variant_images|image|mimes:jpeg,png,jpg|max:2048',
            'variant_images.*.color' => 'nullable|string|max:50',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id'
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm',
            'status.required' => 'Trạng thái là bắt buộc',
            'price.required' => 'Giá bán là bắt buộc',
            'quantity.required' => 'Số lượng tồn kho là bắt buộc',
            'sku.unique' => 'Mã SKU đã tồn tại trong hệ thống'
        ]);

        // Custom validation: Check if color needs images after deletion
        if ($request->color_name) {
            // Get current images that will remain after deletion
            $imagesToDelete = $request->delete_images ?? [];
            $remainingImages = ProductImage::where('product_id', $request->product_id)
                ->where(function($query) use ($request) {
                    $query->where('color', $request->color_name)
                          ->orWhereNull('color'); // General images
                })
                ->whereNotIn('id', $imagesToDelete)
                ->exists();
                
            // Check if we have new images being uploaded
            $hasNewImages = $request->variant_images && count($request->variant_images) > 0;
            
            // If no remaining images and no new images
            if (!$remainingImages && !$hasNewImages) {
                return response()->json([
                    'success' => false,
                    'message' => 'Màu "' . $request->color_name . '" sẽ không còn ảnh nào sau khi xóa. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.',
                    'errors' => ['variant_images' => ['Màu "' . $request->color_name . '" sẽ không còn ảnh nào sau khi xóa. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.']]
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Update variant
            $productVariant->update($request->only([
                'product_id', 'size', 'color', 'color_name', 'status', 'price', 'quantity', 'sku'
            ]));

            // Handle image deletion
            if ($request->delete_images) {
                $imagesToDelete = ProductImage::whereIn('id', $request->delete_images)->get();
                foreach ($imagesToDelete as $image) {
                    // Delete files from storage
                    if ($image->image_path && Storage::exists($image->image_path)) {
                        Storage::delete($image->image_path);
                    }
                    if ($image->image_path_medium && Storage::exists($image->image_path_medium)) {
                        Storage::delete($image->image_path_medium);
                    }
                    if ($image->image_path_small && Storage::exists($image->image_path_small)) {
                        Storage::delete($image->image_path_small);
                    }
                    $image->delete();
                }
            }

            // Process and save new variant images
            if ($request->variant_images) {
                foreach ($request->variant_images as $imageData) {
                    if (isset($imageData['file'])) {
                        $imagePaths = $this->processAndSaveProductImage($imageData['file'], 'variant');
                        
                        ProductImage::create([
                            'product_id' => $productVariant->product_id,
                            'image_path' => $imagePaths['original'],
                            'image_path_medium' => $imagePaths['medium'],
                            'color' => $imageData['color'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biến thể sản phẩm đã được cập nhật thành công',
                'redirect' => route('admin.product-variants.index', ['product_id' => $productVariant->product_id])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Biến thể sản phẩm đã được xóa thành công'
        ]);
    }

    private function processAndSaveProductImage($image, $type)
    {
        $timestamp = Carbon::now()->format('YmdHis');
        $randomString = Str::random(10);
        $originalFileName = $image->getClientOriginalName();
        $extension = $image->getClientOriginalExtension();

        // Create a unique file name
        $fileName = $timestamp . '_' . $randomString . '.' . $extension;

        // Define the file paths
        $originalPath = 'products/original/' . $fileName;
        $mediumPath = 'products/medium/' . $fileName;

        // Save the original image
        Storage::disk('public')->put($originalPath, file_get_contents($image));

        // Resize and save the medium image
        $mediumImage = Image::make($image)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        Storage::disk('public')->put($mediumPath, (string) $mediumImage->encode());

        return [
            'original' => $originalPath,
            'medium' => $mediumPath
        ];
    }

    // Helper method để check image requirement
    private function checkColorImageRequirement($productId, $colorName, $newImages = [])
    {
        // Check existing images for this color or general images
        $hasExistingImages = ProductImage::where('product_id', $productId)
            ->where(function($query) use ($colorName) {
                $query->where('color', $colorName)
                      ->orWhereNull('color'); // General images
            })
            ->exists();
        
        // If has existing images, requirement is met
        if ($hasExistingImages) {
            return true;
        }
        
        // Check if uploading new images
        $hasNewImages = count($newImages) > 0;
        
        return $hasNewImages;
    }

    private function generateSKU($request)
    {
        $product = Product::find($request->product_id);
        return Str::slug($product->name . '-' . $request->size . '-' . $request->color_name) . '-' . Str::random(3);
    }
}
