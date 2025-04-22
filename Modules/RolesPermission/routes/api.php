<?php

use Illuminate\Support\Facades\Route;
use Modules\RolesPermission\Http\Controllers\RolesPermissionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rolespermission', RolesPermissionController::class)->names('rolespermission');
});
