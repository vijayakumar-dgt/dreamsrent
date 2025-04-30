<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::group(['middleware' => ['setLocale', 'checkInstallerStatus']], function () {

    Route::group(['prefix' => 'admin','middleware' => 'admin'], function () {
           //Report
           Route::get('income-report', [ReportController::class, 'incomeReport'])->name('admin.income-report');
           Route::get('/fetch-filtered-bookings', [ReportController::class, 'fetchFilteredBookings'])->name('fetch.filtered.bookings');
           Route::get('earning-report', [ReportController::class, 'earningReport'])->name('admin.earning-report');
           Route::get('/earnings/monthly', [ReportController::class, 'getMonthlyEarnings']);
           Route::get('/earnings/breakdown', [ReportController::class, 'getEarningsBreakdown']);
    });
});
