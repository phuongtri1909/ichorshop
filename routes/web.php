<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('products/{slug}', [HomeController::class, 'productDetails'])->name('product.details');

Route::group(['middleware' => 'auth'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('login', function () {
        return view('client.pages.auth.login');
    })->name('login');

    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::get('/register', function () {
        return view('client.pages.auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/forgot-password', function () {
        return view('client.pages.auth.forgot-password');
    })->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');

    Route::get('/reset-password/{token}', function ($token) {
        return view('client.pages.auth.reset-password', ['token' => $token]);
    })->name('reset-password');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');

    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});
