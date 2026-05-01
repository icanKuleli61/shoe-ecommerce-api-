<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;

Route::prefix('locations')->group(function() {

    Route::get('/cities',[LocationController::class,'cities']);

    Route::get('/districts/{city_id}',[LocationController::class,'districts'])
    ->whereNumber('city_id');

    Route::get('/neighborhoods/{district_id}',[LocationController::class,'neighborhoods'])
    ->whereNumber('district_id');


});


