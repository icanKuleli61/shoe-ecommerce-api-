<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VariantSizeController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;

Route::prefix('locations')->group(function () {

    Route::get('/cities', [LocationController::class, 'cities']);

    Route::get('/districts/{city_id}', [LocationController::class, 'districts'])
        ->whereNumber('city_id');

    Route::get('/neighborhoods/{district_id}', [LocationController::class, 'neighborhoods'])
        ->whereNumber('district_id');
});


Route::prefix('addresses')->group(function () {

    Route::post('/', [AddressController::class, 'store']);
    Route::get('/', [AddressController::class, 'index']);
    Route::put('/{id}', [AddressController::class, 'update']);
    Route::delete('/{id}', [AddressController::class, 'destroy']); // ✅ düzeltildi
});


Route::prefix('products')->group(function () {

    // 🔥 product
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{slug}', [ProductController::class, 'show']);
    Route::patch('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::patch('/{id}/restore', [ProductController::class, 'restore']);

    // 🔥 size
    Route::patch('/sizes/{id}', [VariantSizeController::class, 'update']);
    Route::post('/sizes', [VariantSizeController::class, 'store']);
    Route::delete('/sizes/{id}', [VariantSizeController::class, 'destroy']);

    // 🔥 variant
    Route::post('/variants', [ProductVariantController::class, 'store']);
    Route::delete('/variants/{id}', [ProductVariantController::class, 'destroy']);

    Route::get('{productId}/reviews', [ReviewController::class, 'index']);
});


Route::prefix('images')->group(function () {

    Route::post('/', [ProductImageController::class, 'store']);

});

Route::prefix('cart')->group(function () {

    Route::get('/', [CartController::class, 'index']);       

    Route::post('/add', [CartController::class, 'add']);     

    Route::patch('/{id}', [CartController::class, 'update']); 

    Route::delete('/{id}', [CartController::class, 'destroy']); 

    Route::delete('/', [CartController::class, 'clear']);    

});

Route::prefix('orders')->group(function () {

    Route::post('/', [OrderController::class, 'store']);
    Route::post('/{id}/pay', [OrderController::class, 'pay']);

});

Route::post('/reviews', [ReviewController::class, 'store']);

Route::get('/favorites', [FavoriteController::class, 'index']);

