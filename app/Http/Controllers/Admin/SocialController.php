<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Social;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SocialController extends Controller
{
    /**
     * Hiển thị danh sách các mạng xã hội
     */
    public function index()
    {
        $socials = Social::orderBy('name', 'asc')->get();
        
        return view('admin.pages.socials.index', compact('socials'));
    }

    /**
     * Hiển thị form tạo mới mạng xã hội
     */
    public function create()
    {
        return view('admin.pages.socials.create');
    }

    /**
     * Lưu mạng xã hội mới vào database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'icon' => 'required|file|mimes:svg|max:100',
        ],
        [
            'name.required' => 'Tên mạng xã hội là bắt buộc.',
            'name.string' => 'Tên mạng xã hội phải là một chuỗi.',
            'name.max' => 'Tên mạng xã hội không được vượt quá 255 ký tự.',
            'link.url' => 'Liên kết phải là một URL hợp lệ.',
            'link.max' => 'Liên kết không được vượt quá 255 ký tự.',
            'link.required' => 'Liên kết là bắt buộc.',
            'icon.required' => 'Biểu tượng là bắt buộc.',
            'icon.file' => 'Biểu tượng phải là một tệp.',
            'icon.mimes' => 'Biểu tượng phải có định dạng svg.',
            'icon.max' => 'Biểu tượng không được vượt quá 100 KB.',
        ]);

        // Tạo key từ tên
        $key = Str::slug($request->name);
        
        // Kiểm tra key đã tồn tại chưa
        $existingKey = Social::where('key', $key)->first();
        if ($existingKey) {
            $key = $key . '-' . uniqid();
        }

        $iconPath = null;
        if ($request->hasFile('icon')) {
            // Đảm bảo thư mục tồn tại
            $path = storage_path('app/public/socials');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            $svgFile = $request->file('icon');
            $fileName = $key . '-' . time() . '.svg';
            
            // Di chuyển file vào thư mục storage
            $svgFile->move($path, $fileName);
            
            // Đường dẫn để lưu vào database và truy cập trên web
            $iconPath = 'storage/socials/' . $fileName;
        }
        
        Social::create([
            'name' => $request->name,
            'link' => $request->link,
            'icon' => $iconPath,
            'key' => $key,
        ]);

        return redirect()->route('admin.socials.index')
            ->with('success', 'Đã thêm mạng xã hội mới thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa mạng xã hội
     */
    public function edit(Social $social)
    {
        return view('admin.pages.socials.edit', compact('social'));
    }

    /**
     * Cập nhật thông tin mạng xã hội trong database
     */
    public function update(Request $request, Social $social)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'icon' => 'nullable|file|mimes:svg|max:100',
        ],
        [
            'name.required' => 'Tên mạng xã hội là bắt buộc.',
            'name.string' => 'Tên mạng xã hội phải là một chuỗi.',
            'name.max' => 'Tên mạng xã hội không được vượt quá 255 ký tự.',
            'link.url' => 'Liên kết phải là một URL hợp lệ.',
            'link.max' => 'Liên kết không được vượt quá 255 ký tự.',
            'link.required' => 'Liên kết là bắt buộc.',
            'icon.file' => 'Biểu tượng phải là một tệp.',
            'icon.mimes' => 'Biểu tượng phải có định dạng svg.',
            'icon.max' => 'Biểu tượng không được vượt quá 100 KB.',
        ]);

        $updateData = [
            'name' => $request->name,
            'link' => $request->link,
        ];

        if ($request->hasFile('icon')) {
            // Xóa file cũ nếu tồn tại
            if ($social->icon) {
                $oldPath = public_path($social->icon);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Đảm bảo thư mục tồn tại
            $path = storage_path('app/public/socials');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            $svgFile = $request->file('icon');
            $fileName = $social->key . '-' . time() . '.svg';
            
            // Di chuyển file vào thư mục storage
            $svgFile->move($path, $fileName);
            
            // Đường dẫn để lưu vào database
            $updateData['icon'] = 'storage/socials/' . $fileName;
        }

        $social->update($updateData);

        return redirect()->route('admin.socials.index')
            ->with('success', 'Đã cập nhật mạng xã hội thành công!');
    }

    /**
     * Xóa mạng xã hội khỏi database
     */
    public function destroy(Social $social)
    {
        // Xóa file icon nếu tồn tại
        if ($social->icon) {
            $iconPath = public_path($social->icon);
            if (file_exists($iconPath)) {
                unlink($iconPath);
            }
        }
        
        $social->delete();
        
        return redirect()->route('admin.socials.index')
            ->with('success', 'Đã xóa mạng xã hội thành công!');
    }
}