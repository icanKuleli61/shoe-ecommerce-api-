<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\AddressController;

// 🔥 LOCATION
Route::prefix('locations')->group(function () {

    Route::get('/cities',[LocationController::class,'cities']);

    Route::get('/districts/{city_id}',[LocationController::class,'districts'])
        ->whereNumber('city_id');

    Route::get('/neighborhoods/{district_id}',[LocationController::class,'neighborhoods'])
        ->whereNumber('district_id');

});

// 🔥 ADDRESS
Route::prefix('addresses')->group(function () {

    Route::post('/', [AddressController::class, 'store']);
    Route::get('/', [AddressController::class, 'index']);
    Route::put('/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);

});