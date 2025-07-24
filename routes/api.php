<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes with rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    // Vehicle listing endpoints
    Route::get('/vehicles', [\App\Http\Controllers\HomeController::class, 'vehicleListApi']);
    Route::get('/vehicle-details/{slug}', [\App\Http\Controllers\HomeController::class, 'vehicleDetailsApi']);
    Route::get('/locations', [\App\Http\Controllers\HomeController::class, 'locationsApi']);
    Route::get('/brands', [\App\Http\Controllers\HomeController::class, 'brandsApi']);
    Route::get('/vehicle-types', [\App\Http\Controllers\HomeController::class, 'vehicleTypesApi']);
});

// Authenticated API routes with stricter rate limiting
Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // User profile endpoints
    Route::prefix('user')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\UserController::class, 'profileApi']);
        Route::put('/profile', [\App\Http\Controllers\UserController::class, 'updateProfileApi']);
        Route::get('/bookings', [\App\Http\Controllers\UserController::class, 'bookingsApi']);
        Route::get('/wishlists', [\App\Http\Controllers\UserController::class, 'wishlistsApi']);
        Route::post('/wishlist/toggle', [\App\Http\Controllers\UserController::class, 'toggleWishlistApi']);
    });
    
    // Booking endpoints
    Route::prefix('bookings')->group(function () {
        Route::post('/calculate-price', [\Modules\Booking\Http\Controllers\UserBookingController::class, 'calculatePriceApi']);
        Route::post('/create', [\Modules\Booking\Http\Controllers\UserBookingController::class, 'createBookingApi']);
        Route::get('/{id}', [\Modules\Booking\Http\Controllers\UserBookingController::class, 'bookingDetailsApi']);
        Route::put('/{id}/cancel', [\Modules\Booking\Http\Controllers\UserBookingController::class, 'cancelBookingApi']);
    });
});

// Admin API routes with authentication and role checking
Route::middleware(['auth:sanctum', 'admin', 'throttle:200,1'])->prefix('admin')->group(function () {
    // Dashboard endpoints
    Route::get('/dashboard/stats', [\App\Http\Controllers\admin\DashboardController::class, 'statsApi']);
    Route::get('/dashboard/recent-bookings', [\App\Http\Controllers\admin\DashboardController::class, 'recentBookingsApi']);
    Route::get('/dashboard/revenue-chart', [\App\Http\Controllers\admin\DashboardController::class, 'revenueChartApi']);
    
    // Vehicle management endpoints
    Route::apiResource('vehicles', \Modules\CarInfo\Http\Controllers\CarInfoController::class);
    Route::apiResource('bookings', \Modules\Booking\Http\Controllers\BookingController::class);
    Route::apiResource('customers', \App\Http\Controllers\CustomerController::class);
});

// Health check endpoint (no rate limiting)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
});