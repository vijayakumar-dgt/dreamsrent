<?php

namespace Modules\CarInfo\Repositories\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\CarColor;
use Modules\CarInfo\Models\CarFuel;
use Modules\CarInfo\Models\CarModel;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Models\DamageType;
use Modules\CarInfo\Models\ExtraService;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\PricingType;
use Modules\CarInfo\Models\SafetyFeature;
use Modules\CarInfo\Models\Transmission;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\TranslationLanguage;

class VehicleFormDataService extends VehicleRepositoryBase
{
    public function getCreateData(): array
    {
        $authUser = currentUser();
        $languageId = $authUser->language_id ?? 1;

        $carTypes = $this->getActiveByLanguage(Cartype::class, $languageId);
        $brands = $this->getActiveByLanguage(Brand::class, $languageId);
        $carModel = CarModel::where('status', 1)->orderBy('id', 'desc')->get();
        $category = $this->getActiveByLanguage(Category::class, $languageId);
        $location = $this->getActiveByLanguage(Location::class, $languageId);
        $carColor = $this->getActiveByLanguage(CarColor::class, $languageId);
        $carFuel = $this->getActiveByLanguage(CarFuel::class, $languageId);
        $transmission = $this->getActiveByLanguage(Transmission::class, $languageId);
        $safetyFeature = $this->getActiveByLanguage(SafetyFeature::class, $languageId);
        $damageTypes = $this->getActiveByLanguage(DamageType::class, $languageId);
        $extraServices = $this->getActiveByLanguage(ExtraService::class, $languageId);

        $extraServiceInfo = VehicleExtraService::where('vehicle_id', null)
            ->whereIn('extra_service_id', $extraServices->pluck('id'))
            ->get();

        $insurances = Insurance::with('insuranceBenefits', 'priceType')
            ->where('language_id', $languageId)
            ->where('status', 1)
            ->get();

        $priceType = PricingType::where('status', 1)->get();
        $currencySymbol = $this->getCurrencySymbol();

        return [
            'carTypes'         => $carTypes,
            'Brands'           => $brands,
            'CarModel'         => $carModel,
            'Category'         => $category,
            'Location'         => $location,
            'CarColor'         => $carColor,
            'CarFuel'          => $carFuel,
            'Transmission'     => $transmission,
            'SafetyFeature'    => $safetyFeature,
            'DamageTypes'      => $damageTypes,
            'ExtraServices'    => $extraServices,
            'ExtraServiceInfo' => $extraServiceInfo,
            'insurances'       => $insurances,
            'priceType'        => $priceType,
            'authUser'         => $authUser,
            'currencySymbol'   => $currencySymbol,
        ];
    }

    public function getEditData(string $slug, Request $request): array
    {
        $languageId = $this->getLanguageIdFromRequest($request);
        $languageCode = $languageId ? TranslationLanguage::find($languageId) : null;

        /** @var VehicleInfo|null $vehicle */
        $vehicle = $this->findVehicleBySlug($slug, $languageId);

        $selectedFeatures = $vehicle && $vehicle->features ? json_decode($vehicle->features, true) : [];
        $vehiclePrices = $vehicle && $vehicle->vehicle_price ? json_decode($vehicle->vehicle_price, true)[0] ?? [] : [];

        $carTypes = $this->getActiveByLanguage(Cartype::class, $languageId);
        $brands = $this->getActiveByLanguage(Brand::class, $languageId);
        $models = collect();
        $category = $this->getActiveByLanguage(Category::class, $languageId);
        $location = $this->getActiveByLanguage(Location::class, $languageId);
        $carFuel = $this->getActiveByLanguage(CarFuel::class, $languageId);
        $carColor = $this->getActiveByLanguage(CarColor::class, $languageId);
        $transmission = $this->getActiveByLanguage(Transmission::class, $languageId);
        $safetyFeature = $this->getActiveByLanguage(SafetyFeature::class, $languageId);
        $damageTypes = $this->getActiveByLanguage(DamageType::class, $languageId);

        $extraLanguageId = $request->query('language_id') ?? currentUser()->language_id;
        $extraServices = $this->getActiveByLanguage(ExtraService::class, $extraLanguageId);

        $extraServiceInfo = $vehicle ? VehicleExtraService::where('vehicle_id', $vehicle->id)
            ->whereIn('extra_service_id', $extraServices->pluck('id'))
            ->get() : collect();

        $authLanguage = currentUser()->language_id;
        $insurances = Insurance::with('insuranceBenefits', 'priceType')
            ->where('language_id', $authLanguage)
            ->where('status', 1)
            ->get();

        $priceType = PricingType::where('status', 1)->get();

        App::setLocale($languageCode->code ?? 'en');

        return [
            'carTypes'         => $carTypes,
            'Brands'           => $brands,
            'Models'           => $models,
            'Category'         => $category,
            'Location'         => $location,
            'CarFuel'          => $carFuel,
            'CarColor'         => $carColor,
            'Transmission'     => $transmission,
            'SafetyFeature'    => $safetyFeature,
            'selectedFeatures' => $selectedFeatures,
            'vehiclePrices'    => $vehiclePrices,
            'ExtraServices'    => $extraServices,
            'ExtraServiceInfo' => $extraServiceInfo,
            'insurances'       => $insurances,
            'priceType'        => $priceType,
            'DamageTypes'      => $damageTypes,
            'query'            => $vehicle,
            'currencySymbol'   => $this->getCurrencySymbol(),
        ];
    }
}
