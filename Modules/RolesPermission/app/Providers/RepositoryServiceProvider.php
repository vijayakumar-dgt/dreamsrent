<?php

namespace Modules\RolesPermission\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\RolesPermission\Repositories\Contracts\RolesPermissionRepositoryInterface;
use Modules\RolesPermission\Repositories\Eloquent\RolesPermissionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    protected function registerBindings(): void
    {
        $this->app->bind(RolesPermissionRepositoryInterface::class, RolesPermissionRepository::class);
    }
}
