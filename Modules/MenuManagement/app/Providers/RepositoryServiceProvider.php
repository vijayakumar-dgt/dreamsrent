<?php

namespace Modules\MenuManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\MenuManagement\Repositories\Contracts\MenuManagementInterface;
use Modules\MenuManagement\Repositories\Eloquent\MenuManagementRepository;

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
        $this->app->bind(MenuManagementInterface::class, MenuManagementRepository::class);
    }
}
