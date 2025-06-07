<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DressStyle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        ]);

        DressStyle::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

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
        ]);

        $dressStyle->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kiểu dáng đã được cập nhật thành công',
            'redirect' => route('admin.dress-styles.index')
        ]);
    }

    public function destroy(DressStyle $dressStyle)
    {
        $dressStyle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kiểu dáng đã được xóa thành công'
        ]);
    }
}