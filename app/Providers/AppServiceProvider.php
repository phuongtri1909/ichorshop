<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
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
        $logoSite = \App\Models\LogoSite::first();
        $logoPath =
            $logoSrc ??
            ($logoSite && $logoSite->logo ? Storage::url($logoSite->logo) : asset('assets/images/logo/logo.png'));
        $faviconPath =
            $faviconSrc ??
            ($logoSite && $logoSite->favicon ? Storage::url($logoSite->favicon) : asset('assets/images/logo/favicon.ico'));
        view()->share('faviconPath', $faviconPath);
        view()->share('logoPath', $logoPath);
    }
}
