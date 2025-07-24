<?php

use Illuminate\Support\Facades\Route;
use Modules\MenuManagement\Http\Controllers\MenuManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('menumanagement', MenuManagementController::class)->names('menumanagement');
});

Route::group(['middleware' => ['setLocale', 'checkInstallerStatus']], function () {

    Route::group(['prefix' => 'admin','middleware' => 'admin'], function () {
        //menu
        Route::get('menu', [MenuManagementController::class, 'menu'])->name('admin.menu')->middleware('permission');
        Route::post('menus/store', [MenuManagementController::class, 'menuStore'])->name('admin.menuStore');
        Route::get('menus/list', [MenuManagementController::class, 'menuList'])->name('admin.menuList');
        Route::post('menus/update', [MenuManagementController::class, 'menuUpdate'])->name('admin.menuUpdate');
        Route::post('menus/delete', [MenuManagementController::class, 'menuDelete'])->name('admin.menuDelete');
        //menu management
        Route::get('menu-management', [MenuManagementController::class, 'menuManagement'])->name('admin.menuManagement')->middleware('permission');
        Route::post('menu-management/update', [MenuManagementController::class, 'menuManagementUpdate'])->name('admin.menuManagementUpdate');
    });
});
