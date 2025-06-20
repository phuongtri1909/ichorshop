<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\NewsletterController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('products/{slug}', [HomeController::class, 'productDetails'])->name('product.details');
Route::get('/product-images/{slug}/{color?}', [ProductController::class, 'getProductImages']);

Route::get('faqs/load-more', [FaqController::class, 'loadMore'])->name('faqs.load-more');

Route::get('categories/{slug}', [HomeController::class, 'categoryProducts'])->name('category.products');
Route::get('new-arrivals', [HomeController::class, 'newArrivals'])->name('new.arrivals');
Route::get('top-selling', [HomeController::class, 'topSelling'])->name('top.selling');
Route::get('search', [HomeController::class, 'search'])->name('search');

Route::post('/newsletter-subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::group(['prefix' => 'user', 'as' => 'user.'], function () {

    Route::get('countries', [UserController::class, 'getCountries'])->name('countries');
    Route::get('states', [UserController::class, 'getStates'])->name('states');
    Route::get('cities', [UserController::class, 'getCities'])->name('cities');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        // Profile pages
        Route::get('my-account', [UserController::class, 'userProfile'])->name('my.account');
        Route::get('orders', [UserController::class, 'orders'])->name('orders');

        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

        Route::get('addresses', [UserController::class, 'addresses'])->name('addresses');

        // Address CRUD routes
        Route::post('addresses', [UserController::class, 'storeAddress'])->name('addresses.store');
        Route::get('addresses/list', [UserController::class, 'addressesList'])->name('addresses.list');
        Route::get('addresses/{address}', [UserController::class, 'showAddress'])->name('addresses.show');
        Route::put('addresses/{address}', [UserController::class, 'updateAddress'])->name('addresses.update');
        Route::post('addresses/{address}/default', [UserController::class, 'setDefaultAddress'])->name('addresses.default');
        Route::delete('addresses/{address}', [UserController::class, 'deleteAddress'])->name('addresses.delete');

        // Location data routes

        Route::post('update-profile', [UserController::class, 'updateProfile'])->name('update.profile');
        Route::post('update-avatar', [UserController::class, 'updateAvatar'])->name('update.avatar');
        Route::post('update-password', [UserController::class, 'updatePassword'])->name('update.password');


        Route::post('/apply-coupon', [CouponController::class, 'apply'])->name('coupon.apply');
        Route::post('/remove-coupon', [CouponController::class, 'remove'])->name('coupon.remove');
        Route::get('/coupon/info', [CouponController::class, 'getAppliedCouponInfo'])->name('coupon.info');
        Route::get('/coupon/available', [CouponController::class, 'getAvailableCoupons'])->name('coupon.available');

        Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
        Route::get('/checkout/address', [CartController::class, 'checkoutAddress'])->name('checkout.address');
        Route::post('/checkout/process', [CartController::class, 'processCheckout'])->name('checkout.process');
        Route::get('/checkout/success/{order}', [CartController::class, 'checkoutSuccess'])->name('checkout.success');
    });
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('login', function () {
        return view('client.pages.auth.login');
    })->name('login');

    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', function () {
        return view('client.pages.auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/forgot-password', function () {
        return view('client.pages.auth.forgot-password');
    })->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');

    Route::get('/reset-password/{token}', function ($token) {
        return view('client.pages.auth.reset-password', ['token' => $token]);
    })->name('reset-password');

    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});
