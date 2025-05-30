<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApiSecretKey;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\FranchiseController;
use App\Http\Controllers\Api\ProvincesController;

Route::group(['middleware' => CheckApiSecretKey::class], function () {
    // Routes cho Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{slug}/products', [CategoryController::class, 'products']);
    });

    // Routes cho Products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/featured', [ProductController::class, 'featured']);
        Route::get('/search', [ProductController::class, 'search']);
        Route::get('/{slug}', [ProductController::class, 'show']);
        Route::get('/{slug}/reviews', [ProductController::class, 'reviews']);
    });

    // Routes cho Banners
    Route::prefix('banners')->group(function () {
        Route::get('/', [BannerController::class, 'index']);
        Route::get('/active', [BannerController::class, 'active']);
    });

    Route::post('shipping/calculate', [ShippingController::class, 'calculate']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::get('/provinces', [ProvincesController::class, 'allProvinces']);
    Route::get('/provinces/{provinceCode}/districts', [ProvincesController::class, 'districts']);
    Route::get('/provinces/{districtCode}/wards', [ProvincesController::class, 'wards']);

    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::get('/featured',[NewsController::class, 'featured']);
        Route::get('/latest', [NewsController::class, 'latest']);
        Route::get('/search', [NewsController::class, 'search']);
        Route::get('/{slug}', [NewsController::class, 'show']);
    });

    Route::prefix('franchises')->name('franchises.')->group(function () {
        Route::get('/', [FranchiseController::class, 'index'])->name('index');
        Route::get('/{slug}', [FranchiseController::class, 'show'])->name('show');
    });

    Route::post('franchises-contact', [FranchiseController::class, 'FranchiseContact'])->name('franchises.contact');

    Route::post('contact', [ContactController::class, 'Contact'])->name('contact');

    Route::prefix('socials')->group(function () {
        Route::get('/', [SocialController::class, 'index']);
    });

    Route::get('/ping', function () {
        return response()->json(['message' => 'pong']);
    });
});
