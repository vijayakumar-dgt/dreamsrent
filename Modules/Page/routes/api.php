<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;
use Modules\Page\Http\Controllers\SectionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('page', PageController::class)->names('page');
});

Route::prefix('page-builder')->group(function () {
    Route::post('/section-list', [SectionController::class, 'index']);
});
