<?php

namespace App\Providers;

use App\Repositories\Contracts\HomeRepositoryInterface;
use App\Repositories\Eloquent\HomeRepository;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(HomeRepositoryInterface::class, HomeRepository::class);
    }
}