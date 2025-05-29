<?php

namespace Modules\CarInfo\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\CarInfo\Repositories\Contracts\BrandRepositoryInterface;
use Modules\CarInfo\Repositories\Eloquent\BrandRepository;

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
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
    }
}
