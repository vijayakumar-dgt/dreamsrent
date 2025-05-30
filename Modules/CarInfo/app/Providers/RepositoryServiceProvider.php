<?php

namespace Modules\CarInfo\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\CarInfo\Repositories\Contracts\CategoryRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\CylinderRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\SafetyFeatureRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\SeasonRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\BrandRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\DamageTypeRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\DoorTypeRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\DriverRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\EnquiryRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\ExtraServiceRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\InspectionRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\LocationRepositoryInterface;
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
use Modules\CarInfo\Repositories\Eloquent\CategoryRepository;
use Modules\CarInfo\Repositories\Eloquent\CylinderRepository;
use Modules\CarInfo\Repositories\Eloquent\DriverRepository;
use Modules\CarInfo\Repositories\Eloquent\EnquiryRepository;
use Modules\CarInfo\Repositories\Eloquent\InspectionRepository;
use Modules\CarInfo\Repositories\Eloquent\LocationRepository;
use Modules\CarInfo\Repositories\Eloquent\SafetyFeatureRepository;
use Modules\CarInfo\Repositories\Eloquent\SeasonRepository;

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
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SeasonRepositoryInterface::class, SeasonRepository::class);
        $this->app->bind(CylinderRepositoryInterface::class, CylinderRepository::class);
        $this->app->bind(SafetyFeatureRepositoryInterface::class, SafetyFeatureRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(InspectionRepositoryInterface::class, InspectionRepository::class);
        $this->app->bind(DriverRepositoryInterface::class, DriverRepository::class);
        $this->app->bind(EnquiryRepositoryInterface::class, EnquiryRepository::class);
    }
}
