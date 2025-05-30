<?php



use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FranchiseController;
use App\Http\Controllers\FranchiseContactController;




Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
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

        Route::resource('categories', CategoryController::class)->except(['show']);

        Route::resource('products', ProductController::class)->except(['show']);

        Route::resource('reviews', ReviewController::class)->except(['show']);

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::resource('news', NewsController::class)->except(['show']);

        Route::resource('banners', BannerController::class)->except(['show']);

        Route::post('/upload-image', [NewsController::class, 'uploadImage'])->name('news.upload.image');

        Route::resource('franchise', FranchiseController::class)->except(['show']);

        Route::get('/franchise-contacts', [FranchiseContactController::class, 'index'])->name('franchise-contacts.index');
        Route::get('/franchise-contacts/{franchiseContact}', [FranchiseContactController::class, 'show'])->name('franchise-contacts.show');
        Route::delete('/franchise-contacts/{franchiseContact}', [FranchiseContactController::class, 'destroy'])->name('franchise-contacts.destroy');
        Route::patch('/franchise-contacts/{franchiseContact}/status', [FranchiseContactController::class, 'updateStatus'])->name('franchise-contacts.update-status');

        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
        Route::patch('/contacts/{contact}/status', [ContactController::class, 'updateStatus'])->name('contacts.update-status');

        Route::resource('socials', SocialController::class)->except(['show']);
    });

    Route::group(['middleware' => 'guest'], function () {
        Route::get('login', function () {
            return view('admin.pages.auth.login');
        })->name('login');

        Route::post('login', [AuthController::class, 'login'])->name('login');
    });
});
