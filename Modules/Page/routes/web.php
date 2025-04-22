<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;
use Modules\Page\Http\Controllers\SectionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('page', PageController::class)->names('page');
});

Route::group(['middleware' => ['setLocale', 'checkInstallerStatus']], function () {
    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::get('pages', [PageController::class, 'index'])->name('admin.pageIndex')->middleware('permission');
        Route::get('add-pages', [PageController::class, 'addPage'])->name('admin.addPage')->middleware('permission');
        Route::get('edit-pages/{slug}', [PageController::class, 'editPage'])->name('admin.editPage')->middleware('permission');
        Route::post('page/store', [PageController::class, 'pageStore'])->name('admin.pageStore');
        Route::post('page/update', [PageController::class, 'pageUpdate'])->name('admin.pageUpdate');
        Route::post('get/page-content', [PageController::class, 'pageContent'])->name('admin.pageContent');
        Route::get('sections', [SectionController::class, 'indexSection'])->name('admin.indexSection')->middleware('permission');
        Route::get('section-list', [SectionController::class, 'indexListSection'])->name('admin.section.index');
        Route::post('section-store', [SectionController::class, 'store']);
    });

    Route::get('edit/check-vehicle', [PageController::class, 'getPageInfo'])->name('get.page');

    Route::prefix('page-builder')->group(function () {
        Route::get('/page-builder-list', [PageController::class, 'indexBuilderList']);
        Route::get('/page-info', [PageController::class, 'pageBuilderApi']);
    });
});
