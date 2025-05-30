<?php

namespace Modules\Booking\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Booking\Repositories\Contracts\UserBookingRepositoryInterface;
use Modules\Booking\Repositories\Eloquent\UserBookingRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void 
    {
        $this->registerBindings();
    }

    public function registerBindings(): void
    {
        $this->app->bind(UserBookingRepositoryInterface::class, UserBookingRepository::class);
    }
}
