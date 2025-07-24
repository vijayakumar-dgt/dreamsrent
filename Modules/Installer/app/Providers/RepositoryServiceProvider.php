<?php

namespace Modules\Installer\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Installer\Repositories\Contracts\PurchaseVerificationInterface;
use Modules\Installer\Repositories\Eloquent\PurchaseVerificationRepository;

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
        $this->app->bind(PurchaseVerificationInterface::class, PurchaseVerificationRepository::class);
    }
}
