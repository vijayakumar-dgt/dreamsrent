<?php

use Illuminate\Support\Facades\Route;
use Modules\MenuManagement\Http\Controllers\MenuManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('menumanagement', MenuManagementController::class)->names('menumanagement');
});
