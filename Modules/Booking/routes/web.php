<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\BookingController;
use Modules\Booking\Http\Controllers\QuotationController;
use Modules\Booking\Http\Controllers\UserBookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['setLocale', 'checkInstallerStatus']], function () {

    Route::post('/get-filter-vehicles', [BookingController::class, 'getFilterVehicles']);
    Route::post('get-customer-details', [BookingController::class, 'getCustomerDetails']);
    Route::post('/get/benefits', [UserBookingController::class, 'getBenefits']);

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::get('reservations', [BookingController::class, 'index'])->name('reservation.index')->middleware('permission');
        Route::get('add-reservation', [BookingController::class, 'create'])->name('reservation.create')->middleware('permission');
        Route::post('store-reservation', [BookingController::class, 'store'])->name('reservation.store');
        Route::get('edit-reservation/{id}', [BookingController::class, 'edit'])->name('reservation.edit')->middleware('permission');
        Route::post('delete-reservation', [BookingController::class, 'delete'])->name('reservation.delete');
        Route::post('complete-reservation', [BookingController::class, 'complete'])->name('reservation.complete');
        Route::post('reservation-list', [BookingController::class, 'bookingList'])->name('reservation.list');
        Route::post('get-reservation-details', [BookingController::class, 'getBookingDetails']);
        Route::get('reservation-details/{id}', [BookingController::class, 'reservationViewDetails'])->name('reservation.details')->middleware('permission');
        Route::post('cancel-booking', [BookingController::class, 'cancelBooking'])->name('reservation.cancel');
    });

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::get('quotations', [QuotationController::class, 'index'])->name('quotations.index')->middleware('permission');
        Route::get('add-quotations', [QuotationController::class, 'create'])->name('quotations.create')->middleware('permission');
        Route::post('store-quotations', [QuotationController::class, 'store'])->name('quotations.store');
        Route::get('edit-quotations/{id}', [QuotationController::class, 'edit'])->name('quotations.edit')->middleware('permission');
        Route::post('delete-quotation', [QuotationController::class, 'delete'])->name('quotations.delete');
        Route::post('quotations-list', [QuotationController::class, 'bookingList'])->name('quotations.list');
        Route::post('get-quotations-details', [BookingController::class, 'getBookingDetails']);
        Route::get('quotations-details/{id}', [QuotationController::class, 'reservationViewDetails'])->name('quotations.details');
    });
});
