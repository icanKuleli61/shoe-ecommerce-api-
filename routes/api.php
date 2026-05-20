<?php
use Illuminate\Http\Request;
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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminUserController;


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

Route::get(
    '/banners',
    [BannerController::class, 'index']
);



Route::prefix('home')

    ->controller(
        HomeController::class
    )

    ->group(function () {

        Route::get(
            '/best-sellers',
            'bestSellers'
        );

        Route::get(
            '/new-arrivals',
            'newArrivals'
        );

        Route::get(
            '/discounted-products',
            'discountedProducts'
        );
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
    Route::prefix('favorites')

        ->controller(
            FavoriteController::class
        )

        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/toggle/{productId}',
                'toggle'
            );
        });

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

        Route::post(

            '/{id}/cancel',

            [OrderController::class, 'cancel']
        );

        Route::post(

            '/{id}/complete',

            [OrderController::class, 'complete']
        );

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
*/

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {


    /*
        |--------------------------------------------------------------------------
        | AdminStatusUser
        |--------------------------------------------------------------------------

    */

    Route::get(

        '/users',

        [AdminUserController::class, 'index']
    );

    Route::patch(

        '/users/{id}/toggle-status',

        [AdminUserController::class, 'toggleStatus']
    );

    /*  

    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/products',
        [ProductController::class, 'adminIndex']
    );

    Route::get(
        '/products/filter',
        [ProductController::class, 'adminFilter']
    );

    Route::get(

        '/products/{id}',

        [ProductController::class, 'adminShow']
    );

    Route::post('/products', [ProductController::class, 'store']);

    Route::patch('/products/{id}', [ProductController::class, 'update']);

    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::patch('/products/{id}/restore', [ProductController::class, 'restore']);

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

    Route::get(

        '/orders',

        [OrderController::class, 'adminIndex']
    );

    Route::get(

        '/orders/{id}',

        [OrderController::class, 'adminShow']
    );

    Route::match(

        ['PATCH', 'POST'],

        '/orders/{id}/status',

        [OrderController::class, 'adminUpdateStatus']
    );
    /*
    |--------------------------------------------------------------------------
    | VARIANTS
    |--------------------------------------------------------------------------
    */

    Route::post('/variants', [ProductVariantController::class, 'store']);

    Route::delete('/variants/{id}', [ProductVariantController::class, 'destroy']);

    Route::patch(
        '/variants/{id}',
        [ProductVariantController::class, 'update']
    );
    Route::patch(

        '/products/{id}/full-update',

        [ProductController::class, 'adminUpdate']
    );


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
    Route::get(

        '/banners',

        [BannerController::class, 'adminIndex']
    );

    Route::post(

        '/banners',

        [BannerController::class, 'store']
    );

    Route::patch(

        '/banners/{id}',

        [BannerController::class, 'update']
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