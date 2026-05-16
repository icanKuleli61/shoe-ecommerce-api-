<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
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
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BannerController;



/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);





/*
|--------------------------------------------------------------------------
| PUBLIC PRODUCT ROUTES
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| BANNERS
|--------------------------------------------------------------------------
*/

Route::get(

    '/banners',

    [BannerController::class, 'index']
);

Route::prefix('products')->group(function () {

    Route::get('/', [ProductController::class, 'index']);

    Route::get('/filter', [ProductController::class, 'filter']);

    Route::get('/{slug}', [ProductController::class, 'show']);

    Route::get('/{productId}/reviews', [ReviewController::class, 'index']);

    Route::get('/{productId}/statistics', [ReviewController::class, 'statistics']);
});



/*
|--------------------------------------------------------------------------
| ATTRIBUTE ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('categories')->group(function () {

    Route::get(
        '/',
        [CategoryController::class, 'index']
    );
});



Route::prefix('brands')->group(function () {

    Route::get(
        '/',
        [BrandController::class, 'index']
    );
});



Route::prefix('colors')->group(function () {

    Route::get(
        '/',
        [ColorController::class, 'index']
    );
});





/*
|--------------------------------------------------------------------------
| PUBLIC LOCATION ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('locations')->group(function () {

    Route::get('/cities', [LocationController::class, 'cities']);

    Route::get('/districts/{city_id}', [LocationController::class, 'districts'])
        ->whereNumber('city_id');

    Route::get('/neighborhoods/{district_id}', [LocationController::class, 'neighborhoods'])
        ->whereNumber('district_id');
});





/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ME
    |--------------------------------------------------------------------------
    */

    Route::get('/me', function () {

        return response()->json([
            'user' => auth()->user()
        ]);
    });


    Route::prefix('checkout')
        ->group(function () {

            Route::get(

                '/validate',

                [
                    CheckoutController::class,
                    'validateCart'
                ]

            );




        });


    Route::get('/profile', [UserController::class, 'profile']);

    Route::put('/profile', [UserController::class, 'update']);

    Route::put('/change-password', [UserController::class, 'changePassword']);


    Route::post(
        '/verify-password',
        [UserController::class, 'verifyPassword']
    );

    Route::prefix('wallet')->group(function () {

        Route::get(

            '/',

            [WalletController::class, 'index']
        );



        Route::post(

            '/deposit',

            [WalletController::class, 'addBalance']
        );
    });




    /*
    |--------------------------------------------------------------------------
    | ADDRESS
    |--------------------------------------------------------------------------
    */

    Route::prefix('addresses')->group(function () {

        Route::post('/', [AddressController::class, 'store']);

        Route::get('/', [AddressController::class, 'index']);

        Route::put('/{id}', [AddressController::class, 'update']);

        Route::delete('/{id}', [AddressController::class, 'destroy']);

        Route::patch(
            '/{id}/default',
            [AddressController::class, 'makeDefault']
        );
    });



    /*
    |--------------------------------------------------------------------------
    | FAVORITES
    |--------------------------------------------------------------------------
    */

    Route::get('/favorites', [FavoriteController::class, 'index']);

    Route::post('/favorites/{productId}', [FavoriteController::class, 'toggle']);





    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    */

    Route::post('/reviews', [ReviewController::class, 'store']);






    /*
    |--------------------------------------------------------------------------
    | CART
    |--------------------------------------------------------------------------
    */

    Route::prefix('cart')->group(function () {

        Route::get('/', [CartController::class, 'index']);

        Route::post('/add', [CartController::class, 'add']);

        Route::patch('/{id}', [CartController::class, 'update']);

        Route::delete('/{id}', [CartController::class, 'destroy']);

        Route::delete('/', [CartController::class, 'clear']);
    });





    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

    Route::prefix('orders')->group(function () {

        Route::post(
            '/',
            [OrderController::class, 'store']
        );



        Route::get(
            '/',
            [OrderController::class, 'index']
        );

        Route::get(
            '/{id}/detail',
            [OrderController::class, 'detail']
        )->whereNumber('id');


        Route::get(
            '/{id}',
            [OrderController::class, 'show']
        )->whereNumber('id');

        Route::patch(
            '/{id}/cancel',
            [OrderController::class, 'cancel']
        )->whereNumber('id');
    });
});





/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
|
| Şimdilik auth var.
| Sonra admin middleware ekleyeceğiz.
|
*/

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    Route::post('/products', [ProductController::class, 'store']);

    Route::patch('/products/{id}', [ProductController::class, 'update']);

    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::patch('/products/{id}/restore', [ProductController::class, 'restore']);





    /*
    |--------------------------------------------------------------------------
    | VARIANTS
    |--------------------------------------------------------------------------
    */

    Route::post('/variants', [ProductVariantController::class, 'store']);

    Route::delete('/variants/{id}', [ProductVariantController::class, 'destroy']);





    /*
    |--------------------------------------------------------------------------
    | SIZES
    |--------------------------------------------------------------------------
    */

    Route::post('/sizes', [VariantSizeController::class, 'store']);

    Route::patch('/sizes/{id}', [VariantSizeController::class, 'update']);

    Route::delete('/sizes/{id}', [VariantSizeController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/categories',
        [CategoryController::class, 'store']
    );

    Route::delete(
        '/categories/{id}',
        [CategoryController::class, 'destroy']
    );


    /*
|--------------------------------------------------------------------------
| BANNERS
|--------------------------------------------------------------------------
*/

    Route::post(

        '/banners',

        [BannerController::class, 'store']
    );



    Route::delete(

        '/banners/{id}',

        [BannerController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | BRAND
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/brands',
        [BrandController::class, 'store']
    );

    Route::delete(
        '/brands/{id}',
        [BrandController::class, 'destroy']
    );



    /*
    |--------------------------------------------------------------------------
    | COLOR
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/colors',
        [ColorController::class, 'store']
    );

    Route::delete(
        '/colors/{id}',
        [ColorController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | IMAGES
    |--------------------------------------------------------------------------
    */

    Route::post('/images', [ProductImageController::class, 'store']);

    Route::get(
        '/dashboard',
        [AdminDashboardController::class, 'index']
    );

});