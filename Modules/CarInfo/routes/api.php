<?php

use Illuminate\Support\Facades\Route;
use Modules\CarInfo\Http\Controllers\BrandController;
use Modules\CarInfo\Http\Controllers\CarColorController;
use Modules\CarInfo\Http\Controllers\CarInfoController;
use Modules\CarInfo\Http\Controllers\CarModelController;
use Modules\CarInfo\Http\Controllers\CarTypeController;
use Modules\CarInfo\Http\Controllers\DriverController;
use Modules\CarInfo\Http\Controllers\LocationController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('carinfo', CarInfoController::class)->names('carinfo');
});

Route::get('/countries', [LocationController::class, 'getCountries']);
Route::post('/states', [LocationController::class, 'getStates']);
Route::post('/cities', [LocationController::class, 'getCities']);
