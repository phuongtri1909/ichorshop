<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CkeditorController;
use App\Http\Controllers\Admin\LogoSiteController;
use App\Http\Controllers\Admin\FranchiseController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\DressStyleController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\FranchiseContactController;

Route::group(['as' => 'admin.'], function () {
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return 'Cache cleared';
    })->name('clear.cache');

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', function () {
            return view('admin.pages.dashboard');
        })->name('dashboard');

        Route::get('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('logo-site', [LogoSiteController::class, 'edit'])->name('logo-site.edit');
        Route::put('logo-site', [LogoSiteController::class, 'update'])->name('logo-site.update');

        Route::resource('categories', CategoryController::class)->except(['show']);

        Route::resource('products', ProductController::class)->except(['show']);

        Route::get('/products/get-variant-component', [ProductController::class, 'getVariantComponent'])->name('products.get-variant-component');
        Route::get('/products/get-image-component', [ProductController::class, 'getImageComponent'])->name('products.get-image-component');
        Route::get('/products/get-image-color-options', [ProductController::class, 'getImageColorOptions'])->name('products.get-image-color-options');
        Route::get('/products/get-existing-images', [ProductController::class, 'getExistingImages'])->name('products.get-existing-images');

        Route::resource('brands', BrandController::class)->except(['show']);
        Route::resource('dress-styles', DressStyleController::class)->except(['show']);
        Route::resource('product-variants', ProductVariantController::class)->except(['show']);

        Route::resource('promotions', PromotionController::class)->except(['show']);
        Route::prefix('promotions')->name('promotions.')->group(function () {
            Route::get('{promotion}/variants', [PromotionController::class, 'variants'])->name('variants');
            Route::post('{promotion}/apply-variants', [PromotionController::class, 'applyToVariants'])->name('apply-variants');
            Route::delete('variant-promotions/{promotionVariant}', [PromotionController::class, 'removeVariant'])->name('remove-variant');
            Route::delete('{promotionId}/remove-product/{productId}', [PromotionController::class, 'removeProductVariants'])
                ->name('remove-product-variants');

            Route::get('product-variants', [PromotionController::class, 'getProductVariants'])->name('product-variants');
        });

        Route::resource('reviews', ReviewController::class)->except(['show']);

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::resource('news', NewsController::class)->except(['show']);

        Route::resource('banners', BannerController::class)->except(['show']);

        Route::post('/upload-image', [NewsController::class, 'uploadImage'])->name('news.upload.image');


        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
        Route::patch('/contacts/{contact}/status', [ContactController::class, 'updateStatus'])->name('contacts.update-status');

        Route::resource('socials', SocialController::class)->except(['show']);
        Route::resource('faqs', FaqController::class)->except(['show']);


        Route::get('/newsletter-subscriptions', [NewsletterController::class, 'index'])->name('newsletter.index');
        Route::delete('/newsletter-subscriptions/{subscription}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        Route::post('/newsletter-subscriptions/export', [NewsletterController::class, 'export'])->name('newsletter.export');

        Route::resource('coupons', CouponController::class)->except(['show']);
        Route::get('coupons/generate-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
        Route::get('coupons/product-variants/{product}', [CouponController::class, 'getProductVariants'])->name('coupons.product-variants');
        Route::get('coupons/{coupon}/send', [CouponController::class, 'showSendForm'])->name('coupons.send.form');
        Route::post('coupons/{coupon}/send', [CouponController::class, 'sendCoupon'])->name('coupons.send');

        Route::get('coupons/load-products', [CouponController::class, 'loadProducts'])->name('coupons.load-products');
        Route::get('coupons/load-users', [CouponController::class, 'loadUsers'])->name('coupons.load-users');
        Route::get('coupons/initial-users', [CouponController::class, 'getInitialUsers'])->name('coupons.initial-users');

        Route::post('ckeditor/upload', [CkeditorController::class, 'upload'])
            ->name('ckeditor.upload');

        Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('setting/order', [SettingController::class, 'updateOrder'])->name('setting.update.order');
        Route::put('setting/smtp', [SettingController::class, 'updateSMTP'])->name('setting.update.smtp');
        Route::put('setting/google', [SettingController::class, 'updateGoogle'])->name('setting.update.google');
        Route::put('setting/paypal', [SettingController::class, 'updatePaypal'])->name('setting.update.paypal');
    });

    Route::group(['middleware' => 'guest'], function () {
        Route::get('login', function () {
            return view('admin.pages.auth.login');
        })->name('login');

        Route::post('login', [AuthController::class, 'login'])->name('login.post');
    });
});
