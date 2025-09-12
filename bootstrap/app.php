<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\UserPermission;
use App\Http\Middleware\Customer;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\SetLocaleAdmin;
use App\Http\Middleware\SetLocaleUser;
use App\Http\Middleware\CheckInstallerStatus;
use App\Http\Middleware\SecurityHeader;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::group(['middleware' => ['web']], function () {
                require __DIR__ . '/../routes/web.php';
                require __DIR__ . '/../routes/admin.php';
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => Admin::class,
            'customer' => Customer::class,
            'setLocale' => SetLocaleAdmin::class,
            'setLocaleUser' => SetLocaleUser::class,
            'maintenance' => MaintenanceMode::class,
            'permission' => UserPermission::class,
            'checkInstallerStatus' => CheckInstallerStatus::class,
            'securityHeader' => SecurityHeader::class
        ]);
    })
    ->withExceptions(function () {
        //
    })->create();
