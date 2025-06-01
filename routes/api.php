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

