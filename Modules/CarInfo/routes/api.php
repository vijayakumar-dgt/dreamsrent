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

Route::post('/get-filter-vehicles', [CarInfoController::class,'getFilterVehicles']);
Route::post('/get-brands', [BrandController::class,'getBrands']);
Route::post('/get-vehicle-types', [CarTypeController::class,'getVehicleTypes']);
Route::post('/get-vehicle-models', [CarModelController::class,'getVehicleModels']);
Route::post('/get-vehicle-colors', [CarColorController::class,'getVehicleColors']);
Route::post('/get-drivers', [DriverController::class,'getDrivers']);

Route::post('vehicle-list-detail-api', [CarInfoController::class,'vehicleDetailsList']);
Route::get('vehicle-list-api', [CarInfoController::class,'vehicleLists']);