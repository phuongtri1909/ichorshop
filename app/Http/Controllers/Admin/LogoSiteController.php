<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\LogoSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class LogoSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the first logo site or create a new one if none exists
        $logoSite = LogoSite::first() ?? new LogoSite();

        return view('admin.pages.logo-site.edit', compact('logoSite'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        // Get the first logo site or create a new one if none exists
        $logoSite = LogoSite::first() ?? new LogoSite();

        return view('admin.pages.logo-site.edit', compact('logoSite'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
        ]);

        // Get existing record or create new
        $logoSite = LogoSite::first();
        if (!$logoSite) {
            $logoSite = new LogoSite();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($logoSite->logo) {
                Storage::delete('public/' . $logoSite->logo);
            }

            // Process and save new logo
            $logoPath = $this->processLogo($request->file('logo'));
            $logoSite->logo = $logoPath;
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            if ($logoSite->favicon) {
                Storage::delete('public/' . $logoSite->favicon);
            }

            // Process and save new favicon
            $faviconPath = $this->processFavicon($request->file('favicon'));
            $logoSite->favicon = $faviconPath;
        }

        $logoSite->save();

        return redirect()->route('admin.logo-site.edit')->with('success', 'Logo và favicon đã được cập nhật thành công');
    }

    /**
     * Process and optimize the uploaded logo
     */
    private function processLogo($image)
    {
        // Tạo thư mục trực tiếp trong storage
        $storagePath = storage_path('app/public/logos');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filename = 'site_logo_' . time() . '.webp';
        $fullPath = $storagePath . '/' . $filename;

        // Resize và lưu trực tiếp vào storage
        $img = Image::make($image->getRealPath())
            ->encode('webp', 100);

        $img->save($fullPath);

        return 'logos/' . $filename; // Trả về path tương đối
    }

    /**
     * Process and optimize the uploaded favicon
     */
    private function processFavicon($image)
    {
        // Tạo thư mục trực tiếp trong storage
        $storagePath = storage_path('app/public/logos');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filename = 'favicon_' . time() . '.webp';
        $fullPath = $storagePath . '/' . $filename;

        // Resize to standard favicon size (32x32)
        $img = Image::make($image->getRealPath())
            ->resize(32, 32)
            ->encode('webp', 90);

        $img->save($fullPath);

        return 'logos/' . $filename;
    }
}
