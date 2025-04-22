<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSetting\Http\Controllers\GeneralSettingController;
use Modules\GeneralSetting\Http\Controllers\BlogsController;
use Modules\GeneralSetting\Http\Controllers\AdminProfileController;


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
    Route::apiResource('generalsetting', GeneralSettingController::class)->names('generalsetting');
});
Route::post('/admin/check-password', [AdminProfileController::class, 'checkPassword']);

    Route::group(['prefix' => 'admin/blogs'], function() {
        Route::post('/list-category', [BlogsController::class, 'index']);
        Route::post('/save-category', [BlogsController::class, 'store']);
        Route::post('/delete-category', [BlogsController::class, 'destroy']);
        Route::post('/change-category-status', [BlogsController::class, 'categoryStatusChange']);
        Route::post('/get-category', [BlogsController::class, 'getCategory']);
        Route::post('/check-unique-category-name', [BlogsController::class, 'checkUniqueCategoryName']);
        Route::post('/check-unique-category-slug', [BlogsController::class, 'checkUniqueCategorySlug']);
        Route::post('/get-blog-category', [BlogsController::class, 'getBlogCategory']);

        Route::post('/list-post', [BlogsController::class, 'listPost']);
        Route::post('/save-post', [BlogsController::class, 'savePost']);
        Route::post('/delete-post', [BlogsController::class, 'deletePost']);
        Route::post('/get-post', [BlogsController::class, 'getPost']);
        Route::post('/change-post-status', [BlogsController::class, 'postStatusChange']);
        Route::post('/check-unique-post-title', [BlogsController::class, 'checkUniquePostTitle']);
        Route::post('/check-unique-post-slug', [BlogsController::class, 'checkUniquePostSlug']);
    });