<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = News::query();

        // Filter theo tiêu đề
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Filter theo trạng thái
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        if ($request->has('featured') && $request->featured !== '') {
            $isFeatured = $request->featured == '1';
            $query->where('is_featured', $isFeatured);
        }

        // Sắp xếp theo ngày tạo mới nhất
        $query->orderBy('created_at', 'desc');

        $news = $query->paginate(10);

        return view('admin.pages.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required|max:500000',
            'avatar' => 'required|image|mimes:jpg,jpeg,png',
            'is_active' => 'nullable',
            'is_featured' => 'nullable',
        ], [
            'title.required' => 'Tiêu đề không được để trống',
            'title.max' => 'Tiêu đề không được vượt quá :max ký tự',
            'content.required' => 'Nội dung không được để trống',
            'content.max' => 'Nội dung không được vượt quá :max ký tự',
            'avatar.required' => 'Ảnh đại diện không được để trống',
            'avatar.image' => 'File phải là ảnh',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpg, jpeg, png',
        ]);

        try {
            // Xử lý và lưu ảnh thumbnail
            $thumbnailPath = null;
            if ($request->hasFile('avatar')) {
                $thumbnailPath = ImageHelper::optimizeAndSave(
                    $request->file('avatar'),
                    'news/thumbnails',
                    250
                );
            }

            if ($request->hasFile('avatar')) {
                $avatarPath = ImageHelper::optimizeAndSave(
                    $request->file('avatar'),
                    'news/avatars',
                );
            }

            // Tạo tin tức mới
            $news = new News([
                'title' => $request->title,
                'slug' => $request->slug ?: Str::slug($request->title),
                'content' => $request->content,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
            ]);

            if ($thumbnailPath) {
                $news->thumbnail = $thumbnailPath;
            }

            if ($avatarPath) {
                $news->avatar = $avatarPath;
            }

            $news->save();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Tin tức đã được tạo thành công',
                    'redirect' => route('admin.news.index')
                ]);
            }

            return redirect()->route('admin.news.index')
                ->with('success', 'Tin tức đã được tạo thành công');
        } catch (\Exception $e) {

            if ($thumbnailPath) {
                ImageHelper::delete($thumbnailPath);
            }
            if ($avatarPath) {
                ImageHelper::delete($avatarPath);
            }
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        return view('admin.pages.news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required|max:500000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png',
            'is_active' => 'nullable',
            'is_featured' => 'nullable',
        ], [
            'title.required' => 'Tiêu đề không được để trống',
            'title.max' => 'Tiêu đề không được vượt quá :max ký tự',
            'content.required' => 'Nội dung không được để trống',
            'content.max' => 'Nội dung không được vượt quá :max ký tự',
            'avatar.image' => 'File phải là ảnh',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpg, jpeg, png',
        ]);


        try {
            // Xử lý và lưu ảnh thumbnail mới nếu có
            if ($request->hasFile('avatar')) {


                $oldThumbnailPath = $news->thumbnail;
                $oldAvatarPath = $news->avatar;

                // Lưu ảnh mới
                $thumbnailPath = ImageHelper::optimizeAndSave(
                    $request->file('avatar'),
                    'news/thumbnails',
                    250
                );

                $avatarPath = ImageHelper::optimizeAndSave(
                    $request->file('avatar'),
                    'news/avatars',
                );

                $news->thumbnail = $thumbnailPath;
                $news->avatar = $avatarPath;
            }

            // Cập nhật thông tin tin tức
            $news->title = $request->title;
            $news->slug = $request->slug ?: Str::slug($request->title);
            $news->content = $request->content;
            $news->is_active = $request->has('is_active');
            $news->is_featured = $request->has('is_featured');

            $news->save();

            // Xóa ảnh cũ nếu có
            if (isset($oldThumbnailPath)) {
                ImageHelper::delete($oldThumbnailPath);
            }
            if (isset($oldAvatarPath)) {
                ImageHelper::delete($oldAvatarPath);
            }

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Tin tức đã được cập nhật thành công',
                    'redirect' => route('admin.news.index')
                ]);
            }

            return redirect()->route('admin.news.index')
                ->with('success', 'Tin tức đã được cập nhật thành công');
        } catch (\Exception $e) {

            if (isset($thumbnailPath)) {
                ImageHelper::delete($thumbnailPath);
            }
            if (isset($avatarPath)) {
                ImageHelper::delete($avatarPath);
            }

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        try {
            $oldAvatarPath = $news->avatar;
            $oldThumbnailPath = $news->thumbnail;

            // Xóa bài viết
            $news->delete();

            // Xóa ảnh cũ nếu có
            if (isset($oldAvatarPath)) {
                ImageHelper::delete($oldAvatarPath);
            }
            if (isset($oldThumbnailPath)) {
                ImageHelper::delete($oldThumbnailPath);
            }

            return redirect()->route('admin.news.index')
                ->with('success', 'Tin tức đã được xóa thành công');
        } catch (\Exception $e) {
            return redirect()->route('admin.news.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
