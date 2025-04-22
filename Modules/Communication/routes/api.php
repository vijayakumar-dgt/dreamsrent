<?php

use Illuminate\Support\Facades\Route;
use Modules\Communication\Http\Controllers\CommunicationController;
use Modules\Communication\Http\Controllers\EmailController;
use Modules\Communication\Http\Controllers\ContactController;

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
    Route::apiResource('communication', CommunicationController::class)->names('communication');
});
Route::prefix('mail')->group(function () {
    Route::post('/sendmail', [EmailController::class, 'sendEmail']);
});
Route::post('contact-message/save', [ContactController::class, 'store'])->name('communication.contact-message.store');
