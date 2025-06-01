<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Check if database and table exist before querying
        $logoSite = null;
        try {
            if (Schema::hasTable('logo_sites')) {
                $logoSite = \App\Models\LogoSite::first();
            }
        } catch (\Exception $e) {
            // Ignore database errors during migration
        }

        $logoPath = $logoSite && $logoSite->logo
            ? Storage::url($logoSite->logo)
            : asset('assets/images/logo/logo.png');

        $faviconPath = $logoSite && $logoSite->favicon
            ? Storage::url($logoSite->favicon)
            : asset('assets/images/logo/favicon.ico');

        view()->share('faviconPath', $faviconPath);
        view()->share('logoPath', $logoPath);
    }
}
