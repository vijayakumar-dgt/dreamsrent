<?php

use Illuminate\Support\Facades\Route;
use Modules\CarInfo\Http\Controllers\BrandController;
use Modules\CarInfo\Http\Controllers\CarInfoController;
use Modules\CarInfo\Http\Controllers\CarTypeController;
use Modules\CarInfo\Http\Controllers\DamageTypeController;
use Modules\CarInfo\Http\Controllers\DoorTypeController;
use Modules\CarInfo\Http\Controllers\LocationController;
use Modules\CarInfo\Http\Controllers\CarColorController;
use Modules\CarInfo\Http\Controllers\CarModelController;
use Modules\CarInfo\Http\Controllers\CylinderController;
use Modules\CarInfo\Http\Controllers\ExtraServiceController;
use Modules\CarInfo\Http\Controllers\SafetyFeatureController;
use Modules\CarInfo\Http\Controllers\TagControlerController;
use Modules\CarInfo\Http\Controllers\CarFuelController;
use Modules\CarInfo\Http\Controllers\CarSeatController;
use Modules\CarInfo\Http\Controllers\CarSteeringController;
use Modules\CarInfo\Http\Controllers\CarTransmissionContollerController;
use Modules\CarInfo\Models\CarSteering;
use Modules\CarInfo\Http\Controllers\SeasonController;
use Modules\CarInfo\Http\Controllers\CategoryController;
use Modules\CarInfo\Http\Controllers\InspectionController;
use Modules\CarInfo\Http\Controllers\DriverController;
use Modules\CarInfo\Http\Controllers\MaintenanceController;
use Modules\CarInfo\Http\Controllers\EnquireController;

Route::group(['middleware' => ['setLocale', 'checkInstallerStatus', 'securityHeader']], function () {

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::get('vehicle-types', [CarTypeController::class, 'carTypes'])->name('cartypes')->middleware('permission');
        Route::post('storetype', [CarTypeController::class, 'storeType'])->name('storetype');
        Route::get('getcartypes', [CarTypeController::class, 'getCarTypes'])->name('getcartype');
        Route::get('getcartype/{id}', [CarTypeController::class, 'getCarType'])->name('getcartype');
        Route::post('update_type', [CarTypeController::class, 'updateType'])->name('update_type');
        Route::post('deletetype', [CarTypeController::class, 'deleteType'])->name('deletetype');
        Route::post('get_cartype_serverside', [CarTypeController::class, 'getCartypeServerside'])->name('get_cartype_serverside');
        // Door Types
        Route::get('door-types', [DoorTypeController::class, 'index'])->name('doorType.index')->middleware('permission');
        Route::post('door-type/save', [DoorTypeController::class, 'store'])->name('doorType.store');
        Route::get('door-type/list', [DoorTypeController::class, 'list'])->name('doorType.list');
        Route::get('door-type/edit/{id}', [DoorTypeController::class, 'edit'])->name('doorType.edit');
        Route::post('door-type/delete', [DoorTypeController::class, 'delete'])->name('doorType.delete');
        // Location Routes
        Route::get('locations', [LocationController::class, 'index'])->name('locations')->middleware('permission');
        Route::post('store_location', [LocationController::class, 'storeLocation'])->name('store_location');
        Route::get('get_locations', [LocationController::class, 'getLocations'])->name('get_locations');
        Route::get('get_location/{id}', [LocationController::class, 'getLocation'])->name('get_location');
        Route::post('delete_location', [LocationController::class, 'deleteLocation'])->name('delete_location');
        //DamageType Routes
        Route::get('damage-types', [DamageTypeController::class, 'index'])->name('damage-types')->middleware('permission');
        Route::post('store_damage_type', [DamageTypeController::class, 'storeDamageType'])->name('store_damage_type');
        Route::get('get_damage_types', [DamageTypeController::class, 'getDamageTypes'])->name('get_damage_types');
        Route::get('get_damage_type/{id}', [DamageTypeController::class, 'getDamageType'])->name('get_damage_type');
        Route::post('delete_damage_type', [DamageTypeController::class, 'deleteDamageType'])->name('delete_damage_type');
        //Tag Routes
        Route::get('tags', [TagControlerController::class, 'index'])->name('tags')->middleware('permission');
        Route::post('store_tag', [TagControlerController::class, 'save'])->name('store_tag');
        Route::get('get_tags', [TagControlerController::class, 'getTags'])->name('get_tags');
        Route::get('get_tag/{id}', [TagControlerController::class, 'getTag'])->name('get_tag');
        Route::post('delete_tag', [TagControlerController::class, 'deleteTag'])->name('delete_tag');
        // Brand
        Route::get('brands', [BrandController::class, 'index'])->name('brand.index')->middleware('permission');
        Route::post('brand/save', [BrandController::class, 'store'])->name('brand.store');
        Route::get('brand/list', [BrandController::class, 'list'])->name('brand.list');
        Route::get('brand/edit/{id}', [BrandController::class, 'edit'])->name('brand.edit');
        Route::post('brand/delete', [BrandController::class, 'delete'])->name('brand.delete');
        // Car Model
        Route::get('vehicle-models', [CarModelController::class, 'index'])->name('carModel.index')->middleware('permission');
        Route::post('vehicle-model/save', [CarModelController::class, 'store'])->name('carModel.store');
        Route::get('vehicle-model/list', [CarModelController::class, 'list'])->name('carModel.list');
        Route::get('vehicle-model/edit/{id}', [CarModelController::class, 'edit'])->name('carModel.edit');
        Route::post('vehicle-model/delete', [CarModelController::class, 'delete'])->name('carModel.delete');
        //Cylinder Routes
        Route::get('cylinders', [CylinderController::class, 'index'])->name('cylinders')->middleware('permission');
        Route::post('store_cylinder_type', [CylinderController::class, 'storeCylinderType'])->name('store_cylinder_type');
        Route::get('get_cylinders', [CylinderController::class, 'getCylinders'])->name('get_cylinders');
        Route::get('get_cylinder/{id}', [CylinderController::class, 'getCylinder'])->name('get_cylinder');
        Route::post('delete_cylinder', [CylinderController::class, 'deleteCylinder'])->name('delete_cylinder');
        Route::post('get_cylinder_serverside', [CylinderController::class, 'getCylinderServerside'])->name('get_cylinder_serverside');
        //Extra Services
        Route::get('extra-services', [ExtraServiceController::class, 'index'])->name('extra_services')->middleware('permission');
        Route::post('store_extra_service', [ExtraServiceController::class, 'storeExtraService'])->name('store_extra_service');
        Route::get('get_extra_services', [ExtraServiceController::class, 'getExtraServices'])->name('get_extra_services');
        Route::get('get_extra_service/{id}', [ExtraServiceController::class, 'getExtraService'])->name('get_extra_service');
        Route::post('delete_extra_service', [ExtraServiceController::class, 'deleteExtraService'])->name('delete_extra_service');
        // Safety Features
        Route::get('safety-features', [SafetyFeatureController::class, 'index'])->name('safetyFeature.index')->middleware('permission');
        Route::post('safety-feature/save', [SafetyFeatureController::class, 'store'])->name('safetyFeature.store');
        Route::get('safety-feature/list', [SafetyFeatureController::class, 'list'])->name('safetyFeature.list');
        Route::get('safety-feature/edit/{id}', [SafetyFeatureController::class, 'edit'])->name('safetyFeature.edit');
        Route::post('safety-feature/delete', [SafetyFeatureController::class, 'delete'])->name('safetyFeature.delete');
        // Car Seat Type
        Route::get('seat-type', [CarSeatController::class, 'index'])->name('carSeat.index')->middleware('permission');
        Route::post('seat-type/store', [CarSeatController::class, 'store'])->name('carSeat.store');
        Route::get('seat-type/datatable', [CarSeatController::class, 'list'])->name('carSeat.list');
        Route::get('seat-type/edit/{id}', [CarSeatController::class, 'edit'])->name('carSeat.edit');
        Route::post('seat-type/update', [CarSeatController::class, 'update'])->name('carSeat.update');
        Route::post('seat-type/delete', [CarSeatController::class, 'delete'])->name('carSeat.delete');
        // Car Color Type
        Route::get('vehicle-color', [CarColorController::class, 'index'])->name('carColor.index')->middleware('permission');
        Route::post('vehicle-color/store', [CarColorController::class, 'store'])->name('carColor.store');
        Route::get('vehicle-color/datatable', [CarColorController::class, 'list'])->name('carColor.list');
        Route::get('vehicle-color/edit/{id}', [CarColorController::class, 'edit'])->name('carColor.edit');
        Route::post('vehicle-color/update', [CarColorController::class, 'update'])->name('carColor.update');
        Route::post('vehicle-color/delete', [CarColorController::class, 'delete'])->name('carColor.delete');
        //Car transmission
        Route::get('vehicle-transmission', [CarTransmissionContollerController::class, 'index'])->name('carTrasmission.index')->middleware('permission');
        Route::post('vehicle-transmission/store', [CarTransmissionContollerController::class, 'store'])->name('carTrasmission.store');
        Route::get('vehicle-transmission/datatable', [CarTransmissionContollerController::class, 'list'])->name('carTrasmission.list');
        Route::get('vehicle-transmission/edit/{id}', [CarTransmissionContollerController::class, 'edit'])->name('carTrasmission.edit');
        Route::post('vehicle-transmission/update', [CarTransmissionContollerController::class, 'update'])->name('carTrasmission.update');
        Route::post('vehicle-transmission/delete', [CarTransmissionContollerController::class, 'delete'])->name('carTrasmission.delete');
        //Car steeringFuel
        Route::get('fuel-type', [CarFuelController::class, 'index'])->name('fuelType.index')->middleware('permission');
        Route::post('fuel-type/store', [CarFuelController::class, 'store'])->name('fuelType.store');
        Route::get('fuel-type/datatable', [CarFuelController::class, 'list'])->name('fuelType.list');
        Route::get('fuel-type/edit/{id}', [CarFuelController::class, 'edit'])->name('fuelType.edit');
        Route::post('fuel-type/update', [CarFuelController::class, 'update'])->name('fuelType.update');
        Route::post('fuel-type/delete', [CarFuelController::class, 'delete'])->name('fuelType.delete');
        //Car Sttering
        Route::get('steering-type', [CarSteeringController::class, 'index'])->name('steeringType.index')->middleware('permission');
        Route::post('steering-type/store', [CarSteeringController::class, 'store'])->name('steeringType.store');
        Route::get('steering-type/datatable', [CarSteeringController::class, 'list'])->name('steeringType.list');
        Route::get('steering-type/edit/{id}', [CarSteeringController::class, 'edit'])->name('steeringType.edit');
        Route::post('steering-type/update', [CarSteeringController::class, 'update'])->name('steeringType.update');
        Route::post('steering-type/delete', [CarSteeringController::class, 'delete'])->name('steeringType.delete');
        //Seasons Routes
        Route::get('seasons', [SeasonController::class, 'index'])->name('seasons')->middleware('permission');
        Route::post('store_season', [SeasonController::class, 'save'])->name('store_season');
        Route::get('get_seasons', [SeasonController::class, 'getSeasons'])->name('get_seasons');
        Route::get('get_season/{id}', [SeasonController::class, 'getSeason'])->name('get_season');
        Route::post('delete_season', [SeasonController::class, 'delete'])->name('delete_season');
        //Car category
        Route::get('category', [CategoryController::class, 'index'])->name('category.index')->middleware('permission');
        Route::post('category/store', [CategoryController::class, 'store'])->name('category.store');
        Route::get('category/datatable', [CategoryController::class, 'list'])->name('category.list');
        Route::get('category/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
        Route::post('category/update', [CategoryController::class, 'update'])->name('category.update');
        Route::post('category/delete', [CategoryController::class, 'delete'])->name('category.delete');
        //Car Inspection
        Route::get('inspection', [InspectionController::class, 'index'])->name('inspection.index')->middleware('permission');
        Route::post('store_inspection', [InspectionController::class, 'save'])->name('inspection.store');
        Route::get('get_inspections', [InspectionController::class, 'getInspections'])->name('get_inspections');
        Route::get('get_inspection/{id}', [InspectionController::class, 'getInspection'])->name('get_inspection');
        Route::post('delete_inspection', [InspectionController::class, 'deleteInspection'])->name('delete_inspection');
        Route::get('get_vehicles', [InspectionController::class, 'getVehicles'])->name('get_vehicles');
        Route::post('/check-vehicle-inspection', [InspectionController::class, 'checkVehicleInspection']);
        // Driver
        Route::get('drivers', [DriverController::class, 'index'])->name('driver.index')->middleware('permission');
        Route::post('driver/save', [DriverController::class, 'store'])->name('driver.store');
        Route::post('driver/list', [DriverController::class, 'list'])->name('driver.list');
        Route::get('driver/edit/{id}', [DriverController::class, 'edit'])->name('driver.edit');
        Route::post('driver/delete', [DriverController::class, 'delete'])->name('driver.delete');
        Route::post('driver/status-change', [DriverController::class, 'changeStatus'])->name('driver.change-status');
        // Maintenance
        Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index')->middleware('permission');
        Route::post('maintenance/save', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('maintenance/list', [MaintenanceController::class, 'list'])->name('maintenance.list');
        Route::get('maintenance/edit/{id}', [MaintenanceController::class, 'edit'])->name('maintenance.edit');
        Route::post('maintenance/delete', [MaintenanceController::class, 'delete'])->name('maintenance.delete');
        //Enquire
        Route::get('enquiry', [EnquireController::class, 'index'])->name('enquiry.index')->middleware('permission');
        Route::post('enquiry/save', [EnquireController::class, 'store'])->name('enquire.store');
        Route::post('enquiry/update', [EnquireController::class, 'update'])->name('enquire.update');
        Route::get('enquiry/list', [EnquireController::class, 'list'])->name('enquiry.list');
        Route::post('enquiry/delete', [EnquireController::class, 'delete'])->name('enquiry.delete');

        // Door Types
        Route::get('vehiclelist', [CarInfoController::class, 'vehiclelist'])->name('vehicle.list')->middleware('permission');
        Route::get('getvehiclelist', [CarInfoController::class, 'getvehiclelist'])->name('getvehiclelist');
        Route::get('vehicleadd', [CarInfoController::class, 'vehicleadd'])->name('vehicle.vehicleadd')->middleware('permission');
        Route::post('vehiclesave', [CarInfoController::class, 'vehiclesave'])->name('vehicle.vehiclesave');

        //cars Information
        Route::post('create/vehicle', [CarInfoController::class, 'saveCarInfo'])->name('craete.car');
        Route::post('update/vehicle', [CarInfoController::class, 'updateCarInfo'])->name('update.car');
        Route::get('check-vehicle', [CarInfoController::class, 'getCarInfo'])->name('get.car');
        Route::get('edit-vehicle/{slug}', [CarInfoController::class, 'vehicleedit'])->name('edit.car');
        Route::get('get-seasonal-info', [CarInfoController::class, 'seasonalInfo'])->name('seasonalInfo');
        Route::get('get-tarrif-info', [CarInfoController::class, 'tarrifInfo'])->name('tarrifInfo');
        Route::get('get-documents-info', [CarInfoController::class, 'documents'])->name('documents');
        Route::get('get-faq-info', [CarInfoController::class, 'faq'])->name('faq');
        Route::get('get-damage-info', [CarInfoController::class, 'damage'])->name('damage');
        Route::get('get-insurance-info', [CarInfoController::class, 'insurance'])->name('insurance');
        Route::get('get-model', [CarInfoController::class, 'getModel']);
        Route::post('vehicle/image/delete', [CarInfoController::class, 'deleteVehicleImage']);
        Route::post('vehicle/policy/delete', [CarInfoController::class, 'deleteVehiclePolicy']);
        Route::post('vehicle-list', [CarInfoController::class, 'vehicleListApi']);
        Route::post('vehicle/delete', [CarInfoController::class, 'delete'])->name('vehicle.delete');
        Route::get('/get-damage-details', [CarInfoController::class, 'getDamageDetails']);
        Route::post('/vehicle/multiple/delete', [CarInfoController::class, 'deleteMultiple'])->name('admin.vehicles.deleteMultiple');
        Route::get('/set-popular', [CarInfoController::class, 'setPopular'])->name('admin.setPopular');
        Route::get('/set-recommended', [CarInfoController::class, 'setRecommended'])->name('admin.setRecommended');
        Route::get('/set-status', [CarInfoController::class, 'setStatus'])->name('admin.setStatus');
    });

    Route::post('/get-brands', [BrandController::class, 'getBrands']);
    Route::post('/get-vehicle-types', [CarTypeController::class, 'getVehicleTypes']);
    Route::post('/get-vehicle-models', [CarModelController::class, 'getVehicleModels']);
    Route::post('/get-vehicle-colors', [CarColorController::class, 'getVehicleColors']);
    Route::post('/get-drivers', [DriverController::class, 'getDrivers']);
    Route::post('/get-driver-details', [DriverController::class, 'getDriverDetails']);
    Route::post('/get-vehicle-extra-services', [ExtraServiceController::class, 'getVehicleExtraServices']);
    Route::post('/get-locations', [LocationController::class, 'getAllLocations']);
});