<?php

namespace Modules\CarInfo\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\CarInfo\Repositories\Contracts\BrandRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\DamageTypeRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\DoorTypeRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\ExtraServiceRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\MaintenanceRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\TagRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleColorRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleFuelRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleModelRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleSeatRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleSteeringRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleTransmissionRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleTypeRepositoryInterface;
use Modules\CarInfo\Repositories\Eloquent\BrandRepository;
use Modules\CarInfo\Repositories\Eloquent\DamageTypeRepository;
use Modules\CarInfo\Repositories\Eloquent\DoorTypeRepository;
use Modules\CarInfo\Repositories\Eloquent\ExtraServiceRepository;
use Modules\CarInfo\Repositories\Eloquent\MaintenanceRepository;
use Modules\CarInfo\Repositories\Eloquent\TagRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleColorRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleFuelRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleModelRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleSeatRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleSteeringRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleTransmissionRepository;
use Modules\CarInfo\Repositories\Eloquent\VehicleTypeRepository;

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
        $this->app->bind(VehicleTransmissionRepositoryInterface::class, VehicleTransmissionRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(DamageTypeRepositoryInterface::class, DamageTypeRepository::class);
        $this->app->bind(DoorTypeRepositoryInterface::class, DoorTypeRepository::class);
        $this->app->bind(VehicleTypeRepositoryInterface::class, VehicleTypeRepository::class);
        $this->app->bind(MaintenanceRepositoryInterface::class, MaintenanceRepository::class);
        $this->app->bind(ExtraServiceRepositoryInterface::class, ExtraServiceRepository::class);
    }
}
