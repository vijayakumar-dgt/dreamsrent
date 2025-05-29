<?php

namespace Modules\CarInfo\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\CarInfo\Repositories\Contracts\BrandRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleColorRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleFuelRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleModelRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleSeatRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleSteeringRepositoryInterface;
use Modules\CarInfo\Repositories\Eloquent\BrandRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleColorRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleFuelRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleModelRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleSeatRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleSteeringRepository;

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
        $this->app->bind(VehicleColorRepositoryInterface::class, VehicleColorRepository::class);
        $this->app->bind(VehicleFuelRepositoryInterface::class, VehicleFuelRepository::class);
        $this->app->bind(VehicleModelRepositoryInterface::class, VehicleModelRepository::class);
        $this->app->bind(VehicleSeatRepositoryInterface::class, VehicleSeatRepository::class);
        $this->app->bind(VehicleSteeringRepositoryInterface::class, VehicleSteeringRepository::class);
    }
}
