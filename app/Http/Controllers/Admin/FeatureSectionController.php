<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureSection;
use App\Models\FeatureItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeatureSectionController extends Controller
{
    /**
     * Hiển thị danh sách các Feature Section
     */
    public function index(Request $request)
    {
        $query = FeatureSection::with('items')
            ->withCount('items');
            
        // Filter theo tên
        if ($request->has('title') && !empty($request->title)) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        
        $featureSections = $query->orderBy('id', 'desc')->paginate(10);
        
        return view('admin.pages.feature.index', compact('featureSections'));
    }

    /**
     * Hiển thị form tạo Feature Section mới
     */
    public function create()
    {
        return view('admin.pages.feature.create');
    }

    /**
     * Lưu Feature Section mới vào database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
        ],[
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'button_text.string' => 'Văn bản nút phải là một chuỗi.',
            'button_text.max' => 'Văn bản nút không được vượt quá 50 ký tự.',
            'button_link.string' => 'Liên kết nút phải là một chuỗi.',
            'button_link.max' => 'Liên kết nút không được vượt quá 255 ký tự.',
        ]);
        
        $featureSection = FeatureSection::create($validated);
        
        return redirect()->route('admin.feature-sections.index')
            ->with('success', 'Feature Section đã được tạo thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa Feature Section
     */
    public function edit(FeatureSection $featureSection)
    {
        return view('admin.pages.feature.edit', compact('featureSection'));
    }

    /**
     * Cập nhật Feature Section
     */
    public function update(Request $request, FeatureSection $featureSection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
        ],[
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'button_text.string' => 'Văn bản nút phải là một chuỗi.',
            'button_text.max' => 'Văn bản nút không được vượt quá 50 ký tự.',
            'button_link.string' => 'Liên kết nút phải là một chuỗi.',
            'button_link.max' => 'Liên kết nút không được vượt quá 255 ký tự.',
        ]);
        
        $featureSection->update($validated);
        
        return redirect()->route('admin.feature-sections.index')
            ->with('success', 'Feature Section đã được cập nhật thành công!');
    }

    /**
     * Xóa Feature Section
     */
    public function destroy(FeatureSection $featureSection)
    {
        // Xóa các item thuộc section
        foreach ($featureSection->items as $item) {
            // Xóa file icon nếu có
            if ($item->icon && Storage::disk('public')->exists($item->icon)) {
                Storage::disk('public')->delete($item->icon);
            }
            $item->delete();
        }
        
        $featureSection->delete();
        
        return redirect()->route('admin.feature-sections.index')
            ->with('success', 'Feature Section đã được xóa thành công!');
    }
    
    /**
     * Hiển thị danh sách các Feature Item thuộc một Feature Section
     */
    public function items(FeatureSection $featureSection)
    {
        $items = $featureSection->items()->orderBy('sort_order')->get();
        
        return view('admin.pages.feature.items.index', compact('featureSection', 'items'));
    }
    
    /**
     * Hiển thị form tạo Feature Item mới
     */
    public function createItem(FeatureSection $featureSection)
    {
        return view('admin.pages.feature.items.create', compact('featureSection'));
    }
    
    /**
     * Lưu Feature Item mới vào database
     */
    public function storeItem(Request $request, FeatureSection $featureSection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|file|mimes:svg',
            'sort_order' => 'nullable|integer|min:0',
        ],[
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'icon.required' => 'Icon là bắt buộc.',
            'icon.file' => 'Icon phải là một file.',
            'icon.mimes' => 'Icon phải có định dạng: svg',
            'sort_order.integer' => 'Thứ tự phải là một số nguyên.',
            'sort_order.min' => 'Thứ tự không được nhỏ hơn 0.',
        ]);
        
        // Xử lý upload icon
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $extension = $file->getClientOriginalExtension();
            
            $iconPath = null;
            
            if ($extension == 'svg') {
                // Xử lý SVG
                $svgContent = file_get_contents($file->getRealPath());
                
                $fileName = 'feature-icons/' . uniqid() . '.svg';
                Storage::disk('public')->put($fileName, $svgContent);
                $iconPath = $fileName;
            } else {
                // Xử lý các loại file khác (jpg, png, etc.)
                $fileName = 'feature-icons/' . uniqid() . '.' . $extension;
                
                // Lưu và resize hình ảnh nếu cần
                $file->storeAs('public', $fileName);
                $iconPath = $fileName;
            }
            
            $validated['icon'] = $iconPath;
        }
        
        // Tự động đặt sort_order nếu không được cung cấp
        if (!isset($validated['sort_order'])) {
            $maxOrder = $featureSection->items()->max('sort_order');
            $validated['sort_order'] = is_null($maxOrder) ? 0 : $maxOrder + 1;
        }
        
        $featureSection->items()->create($validated);
        
        return redirect()->route('admin.feature-sections.items.index', $featureSection)
            ->with('success', 'Feature Item đã được tạo thành công!');
    }
    
    /**
     * Hiển thị form chỉnh sửa Feature Item
     */
    public function editItem(FeatureSection $featureSection, FeatureItem $featureItem)
    {
        return view('admin.pages.feature.items.edit', compact('featureSection', 'featureItem'));
    }
    
    /**
     * Cập nhật Feature Item
     */
    public function updateItem(Request $request, FeatureSection $featureSection, FeatureItem $featureItem)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|file|mimes:svg',
            'sort_order' => 'nullable|integer|min:0',
        ],[
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'icon.file' => 'Icon phải là một file.',
            'icon.mimes' => 'Icon phải có định dạng: svg, jpg, jpeg, png.',
            'sort_order.integer' => 'Thứ tự phải là một số nguyên.',
            'sort_order.min' => 'Thứ tự không được nhỏ hơn 0.',
        ]);
        
        // Xử lý upload icon mới nếu có
        if ($request->hasFile('icon')) {
            // Xóa icon cũ nếu có
            if ($featureItem->icon && Storage::disk('public')->exists($featureItem->icon)) {
                Storage::disk('public')->delete($featureItem->icon);
            }
            
            $file = $request->file('icon');
            $extension = $file->getClientOriginalExtension();
            
            $iconPath = null;
            
            if ($extension == 'svg') {
                // Xử lý SVG
                $svgContent = file_get_contents($file->getRealPath());
            
                
                $fileName = 'feature-icons/' . uniqid() . '.svg';
                Storage::disk('public')->put($fileName, $svgContent);
                $iconPath = $fileName;
            } else {
                // Xử lý các loại file khác (jpg, png, etc.)
                $fileName = 'feature-icons/' . uniqid() . '.' . $extension;
                
                // Lưu và resize hình ảnh nếu cần
                $file->storeAs('public', $fileName);
                $iconPath = $fileName;
            }
            
            $validated['icon'] = $iconPath;
        }
        
        $featureItem->update($validated);
        
        return redirect()->route('admin.feature-sections.items.index', $featureSection)
            ->with('success', 'Feature Item đã được cập nhật thành công!');
    }
    
    /**
     * Xóa Feature Item
     */
    public function destroyItem(FeatureSection $featureSection, FeatureItem $featureItem)
    {
        // Xóa file icon nếu có
        if ($featureItem->icon && Storage::disk('public')->exists($featureItem->icon)) {
            Storage::disk('public')->delete($featureItem->icon);
        }
        
        $featureItem->delete();
        
        return redirect()->route('admin.feature-sections.items.index', $featureSection)
            ->with('success', 'Feature Item đã được xóa thành công!');
    }
    
    /**
     * Sắp xếp lại thứ tự các Feature Item
     */
    public function reorderItems(Request $request, FeatureSection $featureSection)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:feature_items,id',
        ]);
        
        $items = $request->items;
        
        foreach ($items as $index => $itemId) {
            FeatureItem::where('id', $itemId)->update(['sort_order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }
}
