<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});

Route::get('/banners', [BannerController::class, 'getAllBanners']); 
Route::get('/products', [ProductController::class, 'getAllProducts']);
Route::get('/product/{id}', [ProductController::class, 'getProductById']);
Route::get('/product/{id}/related', [ProductController::class, 'getProductsRelatedById']);
Route::get('/categories/{slug}/metadata', [CategoryController::class, 'getCategoryMetadataBySlug']);
Route::post('/cart/mount', [CartController::class, 'mount']);
Route::get('/cart/shipping', [CartController::class, 'shipping']);

Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);