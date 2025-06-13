<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        // Xử lý khi upload từ dialog
        if ($request->hasFile('upload')) {
            // Validate file
            $request->validate([
                'upload' => 'required|image',
            ],[
                'upload.required' => 'Vui lòng chọn tệp để tải lên.',
                'upload.image' => 'Tệp tải lên phải là hình ảnh.'
            ]);

            $file = $request->file('upload');
            
            // Process and save image
            $imagePath = $this->processAndSaveImage($file);
            $url = Storage::url($imagePath);

            // CKEditor requires this response format
            return response()->json([
                'url' => $url,
                'fileName' => basename($imagePath),
                'uploaded' => 1
            ]);
        }
        // Xử lý khi paste hình ảnh từ clipboard
        else if ($request->has('data')) {
            // Get file data
            $file = $request->get('data');
            
            if (preg_match('/^data:image\/(\w+);base64,/', $file, $matches)) {
                $fileData = substr($file, strpos($file, ',') + 1);
                $fileData = base64_decode($fileData);
                
                if ($fileData === false) {
                    return response()->json(['error' => ['message' => 'Không thể giải mã dữ liệu hình ảnh']], 400);
                }
                
                // Create temporary file from base64 data
                $tempFile = tempnam(sys_get_temp_dir(), 'paste_');
                file_put_contents($tempFile, $fileData);
                
                // Process and save image
                $imagePath = $this->processAndSaveImage($tempFile, true);
                $url = Storage::url($imagePath);
                
                // Remove temporary file
                @unlink($tempFile);
                
                return response()->json([
                    'url' => $url,
                    'fileName' => basename($imagePath),
                    'uploaded' => 1
                ]);
            } else {
                return response()->json(['error' => ['message' => 'Định dạng hình ảnh không hợp lệ']], 400);
            }
        }
        
        return response()->json([
            'uploaded' => 0,
            'error' => [
                'message' => 'Không thể tải lên tệp này'
            ]
        ], 400);
    }
    
    /**
     * Process and save image with compression and optimization
     * 
     * @param mixed $imageFile File object or file path
     * @param bool $isPath Whether $imageFile is a file path
     * @return string Path where the image is stored
     */
    private function processAndSaveImage($imageFile, $isPath = false)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "ckeditor_{$timestamp}_{$randomString}";
        
        // Create directory if it doesn't exist
        Storage::disk('public')->makeDirectory("ckeditor/{$yearMonth}");
        
        // Process and compress image
        try {
            $image = $isPath ? Image::make($imageFile) : Image::make($imageFile->getRealPath());
            
            // Resize if too large (max width: 1200px, maintain aspect ratio)
            if ($image->width() > 1200) {
                $image->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Convert to WebP format for better compression
            $image->encode('webp', 85); // 85% quality
            
            // Save the processed image
            $imagePath = "ckeditor/{$yearMonth}/{$fileName}.webp";
            Storage::disk('public')->put($imagePath, $image->stream());
            
            return $imagePath;
        } catch (\Exception $e) {
            // Nếu xử lý hình ảnh thất bại, lưu file gốc
            if (!$isPath) {
                $extension = $imageFile->getClientOriginalExtension();
                $imagePath = "ckeditor/{$yearMonth}/{$fileName}.{$extension}";
                Storage::disk('public')->putFileAs("ckeditor/{$yearMonth}", $imageFile, "{$fileName}.{$extension}");
            } else {
                $imagePath = "ckeditor/{$yearMonth}/{$fileName}.png";
                Storage::disk('public')->put($imagePath, file_get_contents($imageFile));
            }
            
            return $imagePath;
        }
    }
}