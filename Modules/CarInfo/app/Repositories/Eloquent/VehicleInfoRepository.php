<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Models\Review;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Wishlist;
use App\Services\ImageResizer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Booking\Models\Booking;
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
use Modules\CarInfo\Models\VehicleDamage;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleFaq;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleInsurance;
use Modules\CarInfo\Models\VehicleMeta;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;
use Modules\CarInfo\Repositories\Contracts\VehicleInfoRepositoryInterface;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;

class VehicleInfoRepository implements VehicleInfoRepositoryInterface
{
    protected ImageResizer $imageResizer;
    private const VEHICLE_IMAGE_PATH = 'vehicles/images';
    private const VEHICLE_IMAGE = 'vehicles/images/';
    private const DATE_FORMAT = 'm/d/Y';
    private const STORAGE = 'storage/';
    private const STORAGES = '/storage/';
    private const VEHICLE_IMAGE_SMALL = 'vehicles/images/small/';
    public const CAR_TYPE = 'carType:id,name';
    public const BRAND = 'brand:id,brand_name';
    public const CATEGORY = 'category:id,name';
    public const MAIN_LOCATION = 'mainLocation:id,name';
    public const COLOR = 'color:id,name,value';
    private const FUEL_TYPE = 'fuel_type:id,fuel_type';
    private const TRANSMISSION = 'transmission:id,name';

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function index(): array
    {
        $langID = currentUser()->language_id ?? 1;

        $vechileName = VehicleInfo::orderBy('id', 'desc')->where("language_id", $langID)->get();
        $vechileType = Cartype::orderBy('id', 'desc')->where("language_id", $langID)->get();
        $vechileLocation = Location::orderBy('id', 'desc')->where("language_id", $langID)->get();

        return [
            'vechileName'     => $vechileName,
            'vechileType'     => $vechileType,
            'vechileLocation' => $vechileLocation,
        ];

    }

    public function createVehicle(): array
    {
        /** @var \App\Models\User|null $authUser  */
        $authUser = currentUser();
        $language_id = $authUser->language_id ?? 1;
        $carTypes = Cartype::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $brands = Brand::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $carModel = CarModel::where('status', 1)->orderBy('id', 'desc')->get();
        $category = Category::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $location = Location::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $carColor = CarColor::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $carFuel = CarFuel::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $transmission = Transmission::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $safetyFeature = SafetyFeature::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $damageTypes = DamageType::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $extraServices = ExtraService::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();

        $query = null;

        $extraServiceInfo = VehicleExtraService::where('vehicle_id', $query)
            ->whereIn('extra_service_id', $extraServices->pluck('id'))
            ->get();

        $insurances = Insurance::with('insuranceBenefits', 'priceType')
            ->where("language_id", $language_id)
            ->where('status', 1)
            ->get();

        $priceType = PricingType::where('status', 1)->get();

        $authUser = currentUser();

        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        $currency = null;

        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
        }

        $currencySymbol = $currency->symbol ?? "$";

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

    public function editVehicle(string $slug, Request $request): array
    {
        $languageId = $this->getLanguageId($request);
        $languageCode = $languageId ? TranslationLanguage::find($languageId) : null;

        $vehicle = $this->getVehicleBySlugAndLanguage($slug, $languageId);

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

        $extraServices = $this->getActiveByLanguage(ExtraService::class, $request->query('language_id') ?? currentUser()->language_id);
        $extraServiceInfo = $vehicle ? VehicleExtraService::where('vehicle_id', $vehicle->id)
            ->whereIn('extra_service_id', $extraServices->pluck('id'))
            ->get() : collect();

        $authLanguage = currentUser()->language_id;
        $insurances = Insurance::with('insuranceBenefits', 'priceType')
            ->where('language_id', $authLanguage)
            ->where('status', 1)
            ->get();

        $priceType = PricingType::where('status', 1)->get();

        app()->setLocale($languageCode->code ?? 'en');

        $currencySymbol = $this->getCurrencySymbol();

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
            'currencySymbol'   => $currencySymbol,
        ];
    }

    /**
     * Get language ID from request or current user.
     */
    private function getLanguageId(Request $request): ?int
    {
        if ($request->has('language_id')) {
            $language = Language::find($request->query('language_id'));
            return $language->language_id ?? null;
        }

        return currentUser()->language_id ?? null;
    }

    /**
     * Get vehicle by slug and language, with fallback logic.
     */
    private function getVehicleBySlugAndLanguage(string $slug, ?int $languageId): ?VehicleInfo
    {
        $vehicle = VehicleInfo::where('slug', $slug)
            ->when($languageId, fn($q) => $q->where('language_id', $languageId))
            ->first();

        if (!$vehicle && $languageId) {
            $base = VehicleInfo::where('slug', $slug)->whereNull('parent_id')->first();
            if ($base) {
                $vehicle = VehicleInfo::firstOrNew([
                    'parent_id'   => $base->id,
                    'language_id' => $languageId,
                ]);
            }
        }

        if (!$vehicle) {
            $base = VehicleInfo::where('slug', $slug)->first();
            if ($base) {
                $vehicle = VehicleInfo::firstOrNew([
                    'parent_id'   => $base->parent_id,
                    'language_id' => $languageId,
                ]);
            }
        }

        return $vehicle;
    }

    /**
     * Get active records by language.
     */
    private function getActiveByLanguage(string $modelClass, ?int $languageId)
    {
        return $modelClass::where('status', 1)
            ->when($languageId, fn($q) => $q->where('language_id', $languageId))
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get currency symbol from settings.
     */
    private function getCurrencySymbol(): string
    {
        $currencySetting = GeneralSetting::where('key', 'currency_symbol')->first();
        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
            return $currency->symbol ?? '$';
        }
        return '$';
    }

    public function createVehicleInfo(Request $request)
    {
        try {
            $authId = Auth::guard('admin')->id();

            $vehiclePriceJson = $this->prepareVehiclePrice($request);
            $slug = Str::slug($request->title);
            $vehicleImagePath = $this->uploadSingleImage($request->file('vehicle_image'), self::VEHICLE_IMAGE_PATH);

            $category = Category::find($request->vehicle_category_id);
            $baseKilo = ($request->unlimited === 'on') ? null : $request->input('basic_kilometer', null);
            $extraKilo = ($request->unlimited === 'on') ? null : $request->input('extra_kilometer', null);

            $vehicleData = [
                "vehicle_image"        => $vehicleImagePath,
                "language_id"          => $request->lang_id,
                "name"                 => $request->title,
                'slug'                 => $slug,
                "perma_link"           => $request->perma_link,
                "type_id"              => $request->vehicle_type_id,
                "brand_id"             => $request->vehicle_brand_id,
                "model_id"             => $request->vehicle_model_id,
                "category_id"          => $request->vehicle_category_id,
                "type"                 => $category?->slug,
                "plate_number"         => $request->plate_number,
                "vin"                  => $request->vin_number,
                "main_location_id"     => $request->main_location_id,
                "other_location"       => $request->other_location,
                "other_location_id"    => $request->other_location_id,
                "fuel_type_id"         => $request->vehicle_fuel_id,
                "odometer"             => $request->odometer,
                "color_id"             => $request->vehicle_color_id,
                "year"                 => $request->vehicle_year,
                "transmission_id"      => $request->vehicle_transmission_id,
                "mileage"              => $request->vehicle_mileage,
                "passenger_capacity"   => $request->vehicle_passenger,
                "water_tight"          => $request->water_tight,
                "sliding"              => $request->sliding,
                "hatch"                => $request->hatch,
                "vehicle_price"        => $vehiclePriceJson,
                "num_seats"            => $request->num_seats,
                "num_doors"            => $request->num_doors,
                "num_airbags"          => $request->num_airbags,
                "vehicle_basekm"       => $baseKilo,
                "vehicle_extrakmprice" => $extraKilo,
                "vehicle_video"        => $request->car_video,
                "vehicle_metatitle"    => $request->seo_title,
                "vehicle_metakeywords" => $request->seo_key,
                "vehicle_metadesc"     => $request->seo_description,
                "features"             => $request->feature_id,
                "description"          => $request->description,
                "created_by"           => $authId,
            ];

            $vehicle = VehicleInfo::create($vehicleData);

            // Handle additional related data
            $this->handleVehicleImages($request, $vehicle);
            $this->handleVehicleDocuments($request, $vehicle);
            $this->handleVehicleFAQs($request, $vehicle);
            $this->handleVehicleInsurance($request, $vehicle);
            $this->handleVehicleTariffs($request, $vehicle);
            $this->handleVehicleSeasonals($request, $vehicle);
            $this->handleExtraService($request, $vehicle);
            $this->handleVehiclesDamage($request, $vehicle);

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.rentals.vehicle_create_success'),
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_create_error'),
            ];
        }
    }

    private function prepareVehiclePrice(Request $request): string
    {
        $prices = [];

        if ($request->has('daily_price')) {
            $prices['daily'] = $request->daily_price;
        }

        if ($request->has('weekly_price')) {
            $prices['weekly'] = $request->weekly_price;
        }

        if ($request->has('montly_price')) {
            $prices['monthly'] = $request->montly_price;
        }

        if ($request->has('yearly_price')) {
            $prices['yearly'] = $request->yearly_price;
        }

        return json_encode([$prices]);
    }


    private function uploadSingleImage(?\Illuminate\Http\UploadedFile $file, string $path): ?string
    {
        if ($file && $file->isValid()) {
            return $this->imageResizer->uploadFile($file, $path);
        }
        return null;
    }

    private function handleVehicleImages(Request $request, VehicleInfo $vehicle)
    {
        $images = $request->file('car_images');
        if (!$images) {
            return;
        }

        $paths = [];
        foreach ((array)$images as $image) {
            $paths[] = $this->imageResizer->uploadFile($image, self::VEHICLE_IMAGE_PATH);
        }

        if ($paths) {
            VehicleMeta::create([
                'vehicle_id' => $vehicle->id,
                'key'        => 'vehicle_image',
                'value'      => json_encode($paths),
            ]);
        }
    }

    private function handleVehicleDocuments(Request $request, VehicleInfo $vehicle)
    {
        $this->handleDocumentUpload($request->file('car_document'), $vehicle->id, 'vehicle_doc', 'vehicles/document');
        $this->handleDocumentUpload($request->file('policy_document'), $vehicle->id, 'vehicle_policy', 'vehicles/policy');
    }

    private function handleDocumentUpload($files, int $vehicleId, string $key, string $path)
    {
        if (!$files) {
            return;
        }

        $paths = [];
        foreach ((array)$files as $file) {
            $paths[] = uploadFile($file, $path);
        }

        if ($paths) {
            VehicleMeta::create([
                'vehicle_id' => $vehicleId,
                'key'        => $key,
                'value'      => json_encode($paths),
            ]);
        }
    }

    private function handleVehicleFAQs(Request $request, VehicleInfo $vehicle)
    {
        $faqs = json_decode($request->input('vehicle_faq', '[]'), true);
        if (!is_array($faqs)) {
            return;
        }

        foreach ($faqs as $faq) {
            if (!empty($faq['id'])) {
                VehicleFaq::where('id', $faq['id'])
                    ->where('vehicle_id', $vehicle->id)
                    ->update([
                        'question' => $faq['question'],
                        'answer'   => $faq['answer'],
                    ]);
            } else {
                VehicleFaq::create([
                    'vehicle_id' => $vehicle->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'],
                ]);
            }
        }
    }

    private function handleVehicleInsurance(Request $request, VehicleInfo $vehicle)
    {
        $insurances = json_decode($request->input('vehicle_insurance', '[]'), true);
        if (!is_array($insurances)) {
            return;
        }

        foreach ($insurances as $insurance) {
            if (!empty($insurance['id']) && !empty($insurance['price']) && !empty($insurance['type'])) {
                $type = in_array(strtolower($insurance['type']), ['%', 'percentage']) ? 'Percentage' : 'Fixed';

                VehicleInsurance::create([
                    'vehicle_id'    => $vehicle->id,
                    'insurances_id' => $insurance['id'],
                    'value'         => $type,
                    'price'         => $insurance['price'],
                ]);
            }
        }
    }

    private function handleVehicleTariffs(Request $request, VehicleInfo $vehicle)
    {
        $tariffs = json_decode($request->input('tariff', '[]'), true);
        if (!is_array($tariffs)) {
            return;
        }

        foreach ($tariffs as $tariff) {
            if (!empty($tariff['id'])) {
                VehicleTarrif::where('id', $tariff['id'])
                    ->where('vehicle_id', $vehicle->id)
                    ->update([
                        'tariff_title'       => $tariff['title'],
                        'tariff_daily_price' => $tariff['daily_price'],
                        'tariff_from_days'   => $tariff['from_days'],
                        'tariff_to_days'     => $tariff['to_days'],
                        'tariff_base_km'     => $tariff['base_km'],
                        'tariff_extra_price' => $tariff['extra_price'],
                    ]);
            } else {
                VehicleTarrif::create([
                    'vehicle_id'         => $vehicle->id,
                    'tariff_title'       => $tariff['title'],
                    'tariff_daily_price' => $tariff['daily_price'],
                    'tariff_from_days'   => $tariff['from_days'],
                    'tariff_to_days'     => $tariff['to_days'],
                    'tariff_base_km'     => $tariff['base_km'],
                    'tariff_extra_price' => $tariff['extra_price'],
                ]);
            }
        }
    }

    private function handleVehicleSeasonals(Request $request, VehicleInfo $vehicle)
    {
        $seasonals = json_decode($request->input('seasonal', '[]'), true);
        if (!is_array($seasonals)) {
            return;
        }

        foreach ($seasonals as $season) {
            $data = [
                'vehicle_id'            => $vehicle->id,
                'seasonal_title'        => $season['title'],
                'seasonal_start_date'   => $season['start_date'],
                'seasonal_end_date'     => $season['end_date'],
                'seasonal_daily_rate'   => $season['daily_rate'],
                'seasonal_weekly_rate'  => $season['weekly_rate'],
                'seasonal_monthly_rate' => $season['monthly_rate'],
                'seasonal_late_fee'     => $season['late_fee'],
            ];

            if (!empty($season['id'])) {
                VehicleSeason::where('id', $season['id'])
                    ->where('vehicle_id', $vehicle->id)
                    ->update($data);
            } else {
                VehicleSeason::create($data);
            }
        }
    }

    private function handleExtraServices(Request $request, VehicleInfo $vehicle)
    {
        $extraServices = json_decode($request->input('extra_services', '[]'), true);
        if (!is_array($extraServices)) {
            return;
        }

        foreach ($extraServices as $service) {
            $existing = VehicleExtraService::where('vehicle_id', $vehicle->id)
                ->where('extra_service_id', $service['service_id'])
                ->first();

            $data = [
                'vehicle_id'       => $vehicle->id,
                'extra_service_id' => $service['service_id'],
                'value'            => $service['value'],
                'price'            => $service['price'],
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                VehicleExtraService::create($data);
            }
        }
    }

    private function handleVehicleDamage(Request $request, VehicleInfo $vehicle)
    {
        $damages = json_decode($request->input('vehicle_damage', '[]'), true);
        if (!is_array($damages)) {
            return;
        }

        $damageImages = $request->allFiles()['damage_image'] ?? [];

        foreach ($damages as $index => $damage) {
            $imageFile = is_array($damageImages) ? ($damageImages[$index] ?? null) : $damageImages;
            $uploadedImage = $damage['image'] ?? null;

            if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
                $uploadedImage = $imageFile->store('vehicles/damage', 'public');
            } elseif (!empty($uploadedImage) && str_starts_with($uploadedImage, 'data:image')) {
                $imageData = explode(',', $uploadedImage)[1];
                $imageName = 'vehicles/damage/' . uniqid() . '.png';
                Storage::disk('public')->put($imageName, base64_decode($imageData));
                $uploadedImage = $imageName;
            }

            $data = [
                'vehicle_id'      => $vehicle->id,
                'damage_type'     => $damage['name'],
                'damage_loaction' => $damage['location'],
                'image'           => $uploadedImage,
                'description'     => $damage['description'],
            ];

            if (!empty($damage['id'])) {
                VehicleDamage::where('id', $damage['id'])
                    ->where('vehicle_id', $vehicle->id)
                    ->update($data);
            } else {
                VehicleDamage::create($data);
            }
        }
    }


    public function updateVehicleInfo(Request $request): array
    {
        try {
            $authId = Auth::guard('admin')->id();
            $vehicle = VehicleInfo::findOrFail($request->vehicle_id);

            $vehiclePriceJson = $this->prepareVehiclesPrice($request);
            $vehicleImagePath = $this->handleVehicleImage($request, $vehicle);
            $baseKilo = $this->getBaseKm($request);
            $extraKilo = $this->getExtraKm($request);
            $categorySlug = $this->getCategorySlug($request->vehicle_category_id);

            $data = $this->prepareVehicleData($request, $authId, $vehicleImagePath, $vehiclePriceJson, $baseKilo, $extraKilo, $categorySlug);

            $update = $this->saveVehicle($request, $vehicle, $data);

            $this->handleVehicleFiles($request, $update);
            $this->handleVehicleFaq($request, $update);
            $this->handleVehiclesInsurance($request, $update);
            $this->handleTariff($request, $update);
            $this->handleSeasonal($request, $update);
            $this->handleExtraServices($request, $update);
            $this->handleVehicleDamage($request, $update);

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.rentals.vehicle_update_success')
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ];
        }
    }

    /* ----------- PRIVATE HELPER METHODS ----------- */

    private function prepareVehiclesPrice(Request $request): string
    {
        $price = [];
        foreach (['daily', 'weekly', 'monthly', 'yearly'] as $period) {
            $key = "{$period}_price";
            if ($request->has($key)) {
                $price[$period] = $request->$key;
            }
        }
        return json_encode([$price]);
    }

    private function handleVehicleImage(Request $request, VehicleInfo $vehicle): ?string
    {
        if ($request->hasFile('vehicle_image')) {
            $file = $request->file('vehicle_image');
            if ($file->isValid()) {
                return $this->imageResizer->uploadFile($file, self::VEHICLE_IMAGE_PATH, $vehicle->vehicle_image ?? null);
            }
        }
        return $vehicle->vehicle_image;
    }

    private function getBaseKm(Request $request): ?int
    {
        return ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('basic_kilometer', null);
    }

    private function getExtraKm(Request $request): ?int
    {
        return ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('extra_kilometer', null);
    }

    private function getCategorySlug(?int $categoryId): ?string
    {
        return Category::find($categoryId)?->slug ?? null;
    }

    private function prepareVehicleData(Request $request, int $authId, ?string $imagePath, string $vehiclePriceJson, $baseKilo, $extraKilo, ?string $categorySlug): array
    {
        $data = [
            "vehicle_image"        => $imagePath,
            "parent_id"            => (int) $request->parent_id,
            "name"                 => $request->title,
            'slug'                 => Str::slug($request->title),
            "perma_link"           => $request->perma_link,
            "type_id"              => $request->vehicle_type_id,
            "brand_id"             => $request->vehicle_brand_id,
            "model_id"             => $request->vehicle_model_id,
            "category_id"          => $request->vehicle_category_id,
            "type"                 => $categorySlug,
            "plate_number"         => $request->plate_number,
            "vin"                  => $request->vin_number,
            "main_location_id"     => $request->main_location_id,
            "other_location_id"    => $request->other_location_id,
            "fuel_type_id"         => $request->vehicle_fuel_id,
            "odometer"             => $request->odometer,
            "color_id"             => $request->vehicle_color_id,
            "year"                 => $request->vehicle_year,
            "transmission_id"      => $request->vehicle_transmission_id,
            "mileage"              => $request->vehicle_mileage,
            "passenger_capacity"   => $request->vehicle_passenger,
            "water_tight"          => $request->water_tight,
            "sliding"              => $request->sliding,
            "hatch"                => $request->hatch,
            "vehicle_price"        => $vehiclePriceJson,
            "num_seats"            => $request->num_seats,
            "num_doors"            => $request->num_doors,
            "num_airbags"          => $request->num_airbags,
            "vehicle_basekm"       => $baseKilo,
            "vehicle_extrakmprice" => $extraKilo,
            "vehicle_video"        => $request->car_video,
            "vehicle_metatitle"    => $request->seo_title,
            "vehicle_metakeywords" => $request->seo_key,
            "vehicle_metadesc"     => $request->seo_description,
            "features"             => $request->feature_id,
            "description"          => $request->description,
            "created_by"           => $authId,
        ];

        if ($request->filled('language_id')) {
            $data['language_id'] = (int) $request->language_id;
        }

        return $data;
    }

    private function saveVehicle(Request $request, VehicleInfo $vehicle, array $data): VehicleInfo
    {
        if ($request->filled('vehicle_id')) {
            $vehicle->update($data);
            return $vehicle;
        }

        return VehicleInfo::create($data);
    }

    /* ----------------- FILE HANDLERS ----------------- */
    private function handleVehicleFiles(Request $request, VehicleInfo $vehicle): void
    {
        $this->handleMultipleUploads($request->file('car_images'), $vehicle, 'vehicle_image', self::VEHICLE_IMAGE_PATH);
        $this->handleMultipleUploads($request->file('policy_document'), $vehicle, 'vehicle_policy', 'vehicles/policy');
        $this->handleMultipleUploads($request->file('car_document'), $vehicle, 'vehicle_doc', 'vehicles/document');
    }

    private function handleMultipleUploads($files, VehicleInfo $vehicle, string $key, string $path): void
    {
        if (empty($files)) {
            return;
        }

        $files = is_array($files) ? $files : [$files];
        $uploadedPaths = [];

        foreach ($files as $file) {
            $uploadedPaths[] = $this->uploadFile($file, $path);
        }

        $meta = VehicleMeta::firstOrCreate(
            ['vehicle_id' => $vehicle->id, 'key' => $key],
            ['value' => json_encode([])]
        );

        $existing = json_decode($meta->value, true) ?? [];
        $meta->update(['value' => json_encode(array_merge($existing, $uploadedPaths))]);
    }

    private function uploadFile($file, string $path): string
    {
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            return $file->store($path, 'public');
        }
        return (string)$file;
    }

    /* ----------------- FAQ / INSURANCE / TARIFF ----------------- */

    private function handleVehicleFaq(Request $request, VehicleInfo $vehicle): void
    {
        if (!$request->has('vehicle_faq')) {
            return;
        }

        $faqs = json_decode($request->vehicle_faq, true);
        if (!is_array($faqs)) {
            return;
        }

        VehicleFaq::where('vehicle_id', $vehicle->id)->delete();
        foreach ($faqs as $faq) {
            VehicleFaq::create([
                'vehicle_id' => $vehicle->id,
                'question'   => $faq['question'],
                'answer'     => $faq['answer'],
            ]);
        }
    }

    private function handleVehiclesInsurance(Request $request, VehicleInfo $vehicle): void
    {
        if (!$request->has('vehicle_insurance')) {
            return;
        }

        $insurances = json_decode($request->vehicle_insurance, true);
        if (!is_array($insurances)) {
            return;
        }


        VehicleInsurance::where('vehicle_id', $vehicle->id)->delete();
        foreach ($insurances as $insurance) {
            if (!empty($insurance['id']) && !empty($insurance['price']) && !empty($insurance['type'])) {
                $type = strtolower($insurance['type']) === '%' || strtolower($insurance['type']) === 'percentage' ? 'Percentage' : 'Fixed';
                VehicleInsurance::create([
                    'vehicle_id'    => $vehicle->id,
                    'insurances_id' => $insurance['id'],
                    'value'         => $type,
                    'price'         => $insurance['price'],
                ]);
            }
        }
    }

    private function handleTariff(Request $request, VehicleInfo $vehicle): void
    {
        if (!$request->has('tariff')) {
            return;
        }

        $tariffs = json_decode($request->tariff, true);
        if (!is_array($tariffs)) {
            return;
        }

        foreach ($tariffs as $tariff) {
            $data = [
                'tariff_title'       => $tariff['title'],
                'tariff_daily_price' => $tariff['daily_price'],
                'tariff_from_days'   => $tariff['from_days'],
                'tariff_to_days'     => $tariff['to_days'],
                'tariff_base_km'     => $tariff['base_km'],
                'tariff_extra_price' => $tariff['extra_price'],
            ];

            if (!empty($tariff['id'])) {
                VehicleTarrif::where('id', $tariff['id'])->where('vehicle_id', $vehicle->id)->update($data);
            } else {
                VehicleTarrif::create(array_merge($data, ['vehicle_id' => $vehicle->id]));
            }
        }
    }

    private function handleSeasonal(Request $request, VehicleInfo $vehicle): void
    {
        if (!$request->has('seasonal')) {
            return;
        }

        $seasonals = json_decode($request->seasonal, true);
        if (!is_array($seasonals)) {
            return;
        }

        foreach ($seasonals as $season) {
            $data = [
                'seasonal_title'        => $season['title'],
                'seasonal_start_date'   => $season['start_date'],
                'seasonal_end_date'     => $season['end_date'],
                'seasonal_daily_rate'   => $season['daily_rate'],
                'seasonal_weekly_rate'  => $season['weekly_rate'],
                'seasonal_monthly_rate' => $season['monthly_rate'],
                'seasonal_late_fee'     => $season['late_fee'],
            ];

            if (!empty($season['id'])) {
                VehicleSeason::where('id', $season['id'])->where('vehicle_id', $vehicle->id)->update($data);
            } else {
                VehicleSeason::create(array_merge($data, ['vehicle_id' => $vehicle->id]));
            }
        }
    }

    private function handleExtraService(Request $request, VehicleInfo $vehicle): void
    {
        if (!$request->has('extra_services')) {
            return;
        }

        $services = json_decode($request->extra_services, true);
        if (!is_array($services)) {
            return;
        }

        VehicleExtraService::where('vehicle_id', $vehicle->id)->delete();

        foreach ($services as $service) {
            VehicleExtraService::create([
                'vehicle_id'       => $vehicle->id,
                'extra_service_id' => $service['service_id'],
                'value'            => $service['value'],
                'price'            => $service['price'],
            ]);
        }
    }

    private function handleVehiclesDamage(Request $request, VehicleInfo $vehicle): void
    {
        if (!$request->has('vehicle_damage')) {
            return;
        }

        $damages = json_decode($request->vehicle_damage, true);
        if (!is_array($damages)) {
            return;
        }

        $damageFiles = $request->allFiles()['damage_image'] ?? null;

        foreach ($damages as $index => $damage) {
            $uploadedImage = $damage['image'] ?? null;

            $imageFile = is_array($damageFiles) ? $damageFiles[$index] ?? null : ($damageFiles ?? null);

            if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
                $uploadedImage = $imageFile->store('vehicles/damages', 'public');
            } elseif (!empty($uploadedImage) && str_starts_with($uploadedImage, 'data:image')) {
                $imageData = explode(',', $uploadedImage)[1];
                $imageName = 'vehicles/damages/' . uniqid() . '.png';
                Storage::disk('public')->put($imageName, base64_decode($imageData));
                $uploadedImage = $imageName;
            }

            if (!empty($damage['id'])) {
                VehicleDamage::where('id', $damage['id'])->where('vehicle_id', $vehicle->id)->update([
                    'damage_type'     => $damage['name'],
                    'damage_loaction' => $damage['location'],
                    'image'           => $uploadedImage,
                    'description'     => $damage['description'],
                ]);
            } else {
                VehicleDamage::create([
                    'vehicle_id'      => $vehicle->id,
                    'damage_type'     => $damage['name'],
                    'damage_loaction' => $damage['location'],
                    'image'           => $uploadedImage,
                    'description'     => $damage['description'],
                ]);
            }
        }
    }

    public function adminVehicleList(Request $request): array
    {
        $response = [
            'code'    => 500,
            'status'  => 'error',
            'message' => __('admin.common.default_retrieve_error'),
        ];

        try {
            /** @var \App\Models\User|null $authUser */
            $authUser = currentUser();
            if (!$authUser) {
                return [
                    'code'    => 401,
                    'message' => __('Unauthorized.'),
                ];
            }

            $languageId = $authUser->language_id;

            $query = VehicleInfo::with([
                self::CAR_TYPE,
                self::BRAND,
                self::CATEGORY,
                self::MAIN_LOCATION,
                self::COLOR
            ])->select([
                "id","vehicle_image","name","slug","type_id","brand_id","category_id","main_location_id",
                "color_id","vehicle_price","vehicle_basekm","created_at","perma_link","model_id","plate_number",
                "vin","other_location_id","fuel_type_id","odometer","year","transmission_id","mileage",
                "passenger_capacity","num_seats","num_doors","num_airbags","vehicle_video","vehicle_extrakmprice",
                "vehicle_metatitle","vehicle_metadesc","vehicle_metakeywords","features","popular","recommended",
                "status"
            ])->where("language_id", $languageId);

            // Apply filters
            $this->applyVehicleFilters($query, $request);

            // Apply sorting
            $this->applyVehicleSorting($query, $request);

            $vehicles = $query->get()->map(fn($vehicle) => $this->transformVehicle($vehicle));

            $response = [
                'code'    => 200,
                'status'  => 'success',
                'message' => __('web.common.default_retrieve_success'),
                'data'    => $vehicles,
            ];
        } catch (\Exception $e) {
            // keep default error response
        }

        return $response;
    }

    /**
     * Apply filters to the vehicle query
     */
    private function applyVehicleFilters($query, Request $request): void
    {
        if (!is_null($request->name)) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if (!is_null($request->vehicle_id) && is_array($request->vehicle_id)) {
            $query->whereIn('id', $request->vehicle_id);
        }

        if (!is_null($request->vehicle_brand_id) && is_array($request->vehicle_brand_id)) {
            $query->whereIn('brand_id', $request->vehicle_brand_id);
        }

        if (!is_null($request->vehicle_type_id) && is_array($request->vehicle_type_id)) {
            $query->whereIn('type_id', $request->vehicle_type_id);
        }

        if (!is_null($request->vehicle_location_id) && is_array($request->vehicle_location_id)) {
            $query->whereIn('main_location_id', $request->vehicle_location_id);
        }

        if (!is_null($request->status)) {
            $query->where('status', $request->status);
        }

        if (!is_null($request->sort_by_date)) {
            $this->applyDateFilter($query, $request->sort_by_date);
        }
    }

    /**
     * Apply sorting logic to the vehicle query
     */
    private function applyVehicleSorting($query, Request $request): void
    {
        $sortBy = $request->sort_by ?? 'ascending';

        match ($sortBy) {
            'latest' => $query->orderBy('created_at', 'desc'),
            'ascending' => $query->orderBy('name', 'asc'),
            'descending' => $query->orderBy('name', 'desc'),
            'last_month' => $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]),
            'last_7_days' => $query->where('created_at', '>=', now()->subDays(7)),
            default => $query->orderBy('name', 'asc'),
        };
    }

    /**
     * Apply date range filter
     */
    private function applyDateFilter($query, string $sortByDate): void
    {
        $dates = explode(' - ', $sortByDate);
        if (count($dates) !== 2) {
            return;
        }

        try {
            $startDate = Carbon::createFromFormat(self::DATE_FORMAT, trim($dates[0]))->startOfDay();
            $endDate = Carbon::createFromFormat(self::DATE_FORMAT, trim($dates[1]))->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } catch (\Exception) {
            // Invalid date format ignored
        }
    }

    /**
     * Transform vehicle for response
     */
    private function transformVehicle($vehicle)
    {
        // Handle vehicle image
        $vehicleImagePath = $vehicle->vehicle_image ?? '';
        $filename = basename($vehicleImagePath);
        $newpath = self::VEHICLE_IMAGE_SMALL . $filename;
        if (file_exists(public_path(self::STORAGE . $newpath))) {
            $vehicleImagePath = $newpath;
        }
        $vehicle->vehicle_image = uploadedAsset($vehicleImagePath);

        // Currency
        $vehicle->currency = getDefaultCurrencySymbol();

        // Multiple images
        $vehicle->multiple_vehicle_images = [];
        if ($vehicleMeta = VehicleMeta::where('vehicle_id', $vehicle->id)->where('key', 'vehicle_image')->first()) {
            $images = $vehicleMeta->value ? json_decode($vehicleMeta->value) : [];
            $vehicle->multiple_vehicle_images = array_map(fn($img) => url('storage/vehicles/' . basename($img)), $images);
        }
        $vehicle->has_multiple_image = count($vehicle->multiple_vehicle_images) > 1;

        // Damage count
        $vehicle->damage_count = VehicleDamage::where('vehicle_id', $vehicle->id)->count();
        $vehicle->created_date = formatDateTime($vehicle->created_at, false);

        return $vehicle;
    }

    public function vehicleLists(Request $request): array
    {
        $query = VehicleInfo::with([
            self::CAR_TYPE,
            self::BRAND,
            self::CATEGORY,
            self::MAIN_LOCATION,
            self::COLOR,
            self::FUEL_TYPE,
            self::TRANSMISSION,
            'reviews:id,vehicle_id,average_ratings'
        ]);

        $authUser = currentUser();

        $lang_id = null;

        if ($authUser && !empty($authUser->language_id)) {
            $lang_id = $authUser->language_id;
        } elseif (App::getLocale()) {
            $currentLocale = App::getLocale();
            $language = TranslationLanguage::where('code', $currentLocale)->first();
            $lang_id = $language->id ?? null;
        } else {
            $defaultLang = Language::select("language_id")->where("default", 1)->first();
            $lang_id = $defaultLang->language_id ?? 1;
        }

        // Initialize variables
        $pickupDatetime = $request->pickup_datetime;
        $returnDatetime = $request->return_datetime;
        $unavailableVehicleIds = [];

        if (!empty($request->location)) {
            $location = Location::where('name', 'LIKE', "%{$request->location}%")->first();

            if (!$location) {
                return [
                    'code'    => 200,
                    'message' => __('web.common.default_retrieve_success'),
                    'data'    => []
                ];
            }

            if (!empty($pickupDatetime) && !empty($returnDatetime)) {
                $unavailableVehicleIds = Booking::where(function ($query) use ($pickupDatetime, $returnDatetime) {
                    $query->where('start_datetime', '<', $returnDatetime)
                        ->where('end_datetime', '>', $pickupDatetime);
                })->pluck('vehicle_id')->toArray();
            }

            $query->where('main_location_id', $location->id);

            if (!empty($unavailableVehicleIds)) {
                $query->whereNotIn('id', $unavailableVehicleIds);
            }
        }

        if (!empty($request->name)) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if (!empty($request->vehicle_id) && is_array($request->vehicle_id)) {
            $query->whereIn('id', $request->vehicle_id);
        }

        if (!empty($request->vehicle_brand_id) && is_array($request->vehicle_brand_id)) {
            $query->whereIn('brand_id', $request->vehicle_brand_id);
        }

        if (!empty($request->vehicle_type_id) && is_array($request->vehicle_type_id)) {
            $query->whereIn('type_id', $request->vehicle_type_id);
        }

        if (!empty($request->year) && is_array($request->year)) {
            $query->whereIn('year', $request->year);
        }

        if (!empty($request->fuel_type_id) && is_array($request->fuel_type_id)) {
            $query->whereIn('fuel_type_id', $request->fuel_type_id);
        }

        if (!empty($request->color_id) && is_array($request->color_id)) {
            $query->whereIn('color_id', $request->color_id);
        }

        if (!empty($request->transmission_id) && is_array($request->transmission_id)) {
            $query->whereIn('transmission_id', $request->transmission_id);
        }

        if (!empty($request->vehicle_capacity) && is_array($request->vehicle_capacity)) {
            $query->whereIn('passenger_capacity', $request->vehicle_capacity);
        }

        if (isset($request->mileage)) {
            if ($request->mileage == 0) {
                $query->where(function ($q) {
                    $q->where('mileage', 0)->orWhereNull('mileage');
                });
            } elseif ($request->mileage == 1) {
                $query->where('mileage', '>', 0);
            }
        }

        if (!empty($request->feature_id) && is_array($request->feature_id)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->feature_id as $feature) {
                    $q->orWhereJsonContains('features', $feature);
                }
            });
        }

        if (!empty($request->rating) && is_array($request->rating)) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    foreach ($request->rating as $rate) {
                        $sub->orWhereBetween('average_ratings', [
                            (float) $rate,
                            (float) $rate + 0.99
                        ]);
                    }
                });
            });
        }

        $fromPrice = $request->from_price ?? 1;
        $toPrice = $request->to_price ?? null;

        if (!empty($toPrice)) {
            // If fromPrice is 0 or not set, default to 1
            $fromPrice = ($fromPrice <= 0) ? 1 : $fromPrice;

            $query->whereRaw("
                CAST(JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) AS UNSIGNED) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) > 0
            ", [
                $fromPrice,
                $toPrice
            ]);
        }

        if (!empty($request->rent_type)) {
            $rentType = $request->rent_type;

            $query->whereRaw("
                JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].$rentType')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].$rentType')) > 0
            ");
        }

        if (!empty($request->vehicle_location_id) && is_array($request->vehicle_location_id)) {
            $query->whereIn('main_location_id', $request->vehicle_location_id);
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->sort_by ?? 'desc';

        switch ($sortBy) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'ascending':
                $query->orderBy('name', 'asc');
                break;

            case 'descending':
                $query->orderBy('name', 'desc');
                break;

            case 'low_to_high':
                $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) AS UNSIGNED) ASC");
                break;

            case 'high_to_low':
                $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) AS UNSIGNED) DESC");
                break;

            default:
                // Default sort if no specific sorting criteria
                $query->orderBy('created_at', 'desc');
                break;
        }


        if (!is_null($request->sort_by_date)) {
            $dates = explode(' - ', $request->sort_by_date);
            if (count($dates) === 2) {
                try {
                    $stDate = $dates[0];
                    $eDate = $dates[1];
                    if ($stDate && $eDate) {
                        $startDate = Carbon::createFromFormat(self::DATE_FORMAT, trim($stDate));
                        $endDate = Carbon::createFromFormat(self::DATE_FORMAT, trim($eDate));
                        if ($startDate && $endDate) {
                            $query->whereBetween('created_at', [
                                $startDate->startOfDay(),
                                $endDate->endOfDay()
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    return [
                        'code'    => 400,
                        'message' => __('Invalid date format.'),
                    ];
                }
            }
        }

        $perPage = $request->paginate ?? 1;

        $vehicles = $query->where("language_id", $lang_id)->where('status', 1)->paginate($perPage);

        $data = $vehicles->map(function (VehicleInfo $vehicle): array {
            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                ->where('key', 'vehicle_image')
                ->first();

            $vehiclePrices = is_string($vehicle->vehicle_price) ? json_decode($vehicle->vehicle_price, true) : [];
            $filteredPrices = [];

            if (!empty($vehiclePrices)) {
                foreach ($vehiclePrices as $price) {
                    foreach ($price as $key => $value) {
                        if ($value > 0) {
                            $filteredPrices[] = [$key => $value]; // Store each key-value pair as a separate object
                        }
                    }
                }
            }
            $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

            if (!empty($vehicle->vehicle_image)) {
                array_unshift($multipleImages, $vehicle->vehicle_image);
            }

            $multipleImages = array_map(function ($img) {
                $img = '/' . ltrim($img, '/'); // Ensure single leading slash

                $img = str_replace(self::VEHICLE_IMAGE, self::VEHICLE_IMAGE_SMALL, $img);

                return url('storage' . $img);
            }, $multipleImages);

            /** @var \App\Models\User $auth */
            $auth = currentUser();
            $authId = $auth->id ?? null;

            $wishlistExists = false;

            if ($authId) {
                $wishlistExists = Wishlist::where("user_id", $authId)
                    ->where("vehicle_id", $vehicle->id)
                    ->exists();
            }

            $currencySymbol = getDefaultCurrencySymbol();

            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;
            $review_count = Review::where("vehicle_id", $vehicle->id)->count();
            $defaultAvatar = asset('/backend/assets/img/default-profile.png');
            $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;

            $avatarImage = $defaultAvatar;

            if ($profileImagePath) {
                $fullImagePath = storage_path('app/public/' . $profileImagePath);
                if (file_exists($fullImagePath)) {
                    $avatarImage = url(self::STORAGES . $profileImagePath);
                }
            }
            return [
                'id'                      => $vehicle->id,
                'name'                    => $vehicle->name,
                'slug'                    => $vehicle->slug,
                'vehicle_image'           => url(self::STORAGES . str_replace(self::VEHICLE_IMAGE, self::VEHICLE_IMAGE_SMALL, $vehicle->vehicle_image)),
                'multiple_vehicle_images' => $multipleImages,
                'has_multiple_image'      => count($multipleImages) > 1,
                'avatar_image'            => $avatarImage,
                'brand'                   => $vehicle->brand->brand_name ?? null,
                'car_type'                => $vehicle->carType->name ?? null,
                'category'                => $vehicle->category->name ?? null,
                'location'                => $vehicle->mainLocation->name ?? null,
                'color'                   => $vehicle->color->name ?? null,
                'color_code'              => $vehicle->color->value ?? null,
                'fuel_type'               => $vehicle->fuel_type->fuel_type ?? null,
                'transmission'            => $vehicle->transmission->name ?? null,
                'year'                    => $vehicle->year,
                'mileage'                 => $vehicle->mileage,
                'passenger_capacity'      => $vehicle->passenger_capacity,
                'num_seats'               => $vehicle->num_seats,
                'num_doors'               => $vehicle->num_doors,
                'num_airbags'             => $vehicle->num_airbags,
                'vehicle_video'           => $vehicle->vehicle_video,
                'features'                => $vehicle->features,
                'currency'                => $currencySymbol,
                'rating'                  => $rating,
                'wishlist'                => $wishlistExists,
                'review_count'            => $review_count,
                'price'                   => !empty($filteredPrices) ? $filteredPrices : null,
                'is_featured'             => $vehicle->feature ?? 0,
                'is_top_rated'            => is_numeric($rating) && $rating >= 4,
                'seo_title'               => $vehicle->vehicle_metatitle,
                'seo_key'                 => $vehicle->vehicle_metakeywords,
                'seo_description'         => $vehicle->vehicle_metadesc,
                'authenticated'           => Auth::guard('web')->check(),
                'created_at'              => $vehicle->created_at,
                'status'                  => $vehicle->status,
            ];
        });


        return [
            'code'       => 200,
            'message'    => __('web.common.default_retrieve_success'),
            'data'       => $data,
            'pagination' => [
                'total'         => $vehicles->total(), // Total vehicles count
                'per_page'      => $vehicles->perPage(), // Vehicles per page
                'current_page'  => $vehicles->currentPage(), // Current page number
                'last_page'     => $vehicles->lastPage(), // Last page number
                'from'          => $vehicles->firstItem(), // First item number on the page
                'to'            => $vehicles->lastItem(), // Last item number on the page
                'next_page_url' => $vehicles->nextPageUrl(), // Next page URL
                'prev_page_url' => $vehicles->previousPageUrl(), // Previous page URL
            ],
        ];
    }

    public function checkVehicle(Request $request): array
    {
        try {
            $vehicleSlug = $request->get('vehicle_slug');

            // Find vehicle directly instead of checking twice
            $vehicle = VehicleInfo::where('slug', $vehicleSlug)->first();

            if (!$vehicle) {
                return [
                    'code'   => 404,
                    'exists' => 'no'
                ];
            }

            return [
                'code'   => 200,
                'exists' => 'yes'
            ];
        } catch (\Exception $e) {
            return [
                'code'  => 500,
                'error' => __('admin.common.default_retrieve_error')
            ];
        }
    }

    public function seasonalInfo(?int $vehicleId): array
    {
        try {
            $vehicleSeasons = VehicleSeason::where("vehicle_id", $vehicleId)->get();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $vehicleSeasons
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function tariffInfo(?int $vehicleId): array
    {
        try {
            $vehicleTrraifs = VehicleTarrif::where("vehicle_id", $vehicleId)->get();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $vehicleTrraifs
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function documents(?int $vehicleId): array
    {
        try {
            $documents = VehicleMeta::where("vehicle_id", $vehicleId)->get();

            // Initialize response structure
            $response = [
                'vehicle_images'   => [],
                'vehicle_docs'     => [],
                'vehicle_policies' => [],
            ];

            foreach ($documents as $document) {
                // Decode JSON value (since values are stored as JSON arrays)
                $values = json_decode($document->value, true);

                if (!is_array($values)) {
                    continue;
                }

                // Categorize based on key
                switch ($document->key) {
                    case 'vehicle_image':
                        $formattedImages = array_map(function ($image) {
                            return asset(self::STORAGE . $image); // Convert to URL format
                        }, $values);

                        $response['vehicle_images'] = array_merge($response['vehicle_images'], $formattedImages);
                        break;

                    case 'vehicle_doc':
                        $response['vehicle_docs'] = array_merge($response['vehicle_docs'], $values);
                        break;

                    case 'vehicle_policy':
                        $response['vehicle_policies'] = array_merge($response['vehicle_policies'], $values);
                        break;

                    default:
                        Log::warning("Unhandled document key: {$document->key}");
                        break;
                }
            }

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $response
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function faq(?int $vehicleId): array
    {
        try {
            $vehicleFaqs = VehicleFaq::where("vehicle_id", $vehicleId)->get();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $vehicleFaqs
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function damage(?int $vehicleId): array
    {
        try {
            $vehicleDamages = VehicleDamage::where("vehicle_id", $vehicleId)->get();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $vehicleDamages
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function insurance(?int $vehicleId): array
    {
        try {
            $vehicleInsurance = VehicleInsurance::where("vehicle_id", $vehicleId)
                ->with(['insurance', 'insuranceBenefits']) // Load related data
                ->get();

            /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\CarInfo\Models\VehicleInsurance> $vehicleInsurance */
            $insuranceData = $vehicleInsurance->map(function (VehicleInsurance $insurance) {
                $benefitCount = $insurance->insuranceBenefits->count();
                $formattedBenefits = str_pad((string)$benefitCount, 2, '0', STR_PAD_LEFT);

                return [
                    'id'             => $insurance->id,
                    'vehicle_id'     => $insurance->vehicle_id,
                    'insurances_id'  => $insurance->insurances_id,
                    'insurance_name' => optional($insurance->insurance)->insurance_name, // Get insurance name
                    'value'          => $insurance->value,
                    'price'          => $insurance->price,
                    'benefits'       => $formattedBenefits, // Count formatted
                    'created_at'     => $insurance->created_at,
                    'updated_at'     => $insurance->updated_at,
                    'deleted_at'     => $insurance->deleted_at,
                ];
            });

            return [
                'code'    => 200,
                'success' => true,
                'data'    => $insuranceData
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function getModel(?int $brandId): array
    {
        try {
            $models = CarModel::where('brand_id', $brandId)->get(['id', 'model_name']);

            return [
                'code'    => 200,
                'success' => true,
                'data'    => $models
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function getTypeAndModel(?int $categoryId): array
    {
        try {
            $types = Cartype::where('category_id', $categoryId)->get(['id', 'name']);
            $brands = Brand::where('category_id', $categoryId)->get(['id', 'brand_name']);

            return [
                'code'    => 200,
                'success' => true,
                'types'   => $types,
                'brands'  => $brands,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function vehicleDetailsList(Request $request): array
    {
        $response = [];

        try {
            $vehicleSlug = $request->vehicle_slug;

            if (!$vehicleSlug) {
                $response = $this->errorResponse('Vehicle ID is required', 400);
            } else {
                $vehicle = VehicleInfo::with([
                    self::CAR_TYPE,
                    self::BRAND,
                    self::CATEGORY,
                    self::MAIN_LOCATION,
                    self::COLOR,
                    self::FUEL_TYPE,
                    self::TRANSMISSION,
                    'extraservices.extraService:id,name,icon,description,image',
                    'faqs:id,vehicle_id,question,answer',
                    'damages:id,Vehicle_id,damage_type,damage_loaction,image,description',
                    'tariffs:id,vehicle_id,tariff_title,tariff_daily_price,tariff_from_days,tariff_to_days,tariff_base_km,tariff_extra_price',
                    'seasonals:id,vehicle_id,seasonal_title,seasonal_start_date,seasonal_end_date,seasonal_daily_rate,seasonal_weekly_rate,seasonal_monthly_rate,seasonal_late_fee',
                    'owner.userDetails:id,user_id,profile_image'
                ])->where('slug', $vehicleSlug)->first();

                if (!$vehicle) {
                    $response = $this->errorResponse('No vehicle found with the provided slug.', 404);
                } else {
                    $data = $this->formatVehicleData($vehicle);
                    $response = [
                        'code'    => 200,
                        'success' => true,
                        'message' => __('admin.common.default_retrieve_success'),
                        'data'    => $data
                    ];
                }
            }
        } catch (\Exception $e) {
            $response = $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }

        return $response;
    }

    /** Helper to format vehicle data */
    private function formatVehicleData(VehicleInfo $vehicle): array
    {
        $featureIds = json_decode($vehicle->features ?? '', true);
        $features = SafetyFeature::whereIn('id', $featureIds)->pluck('feature');

        $vehicleImages = $this->getVehicleMeta($vehicle->id, 'vehicle_image');
        $vehiclePolicies = $this->getVehicleMeta($vehicle->id, 'vehicle_policy');
        $vehicleDocs = $this->getVehicleMeta($vehicle->id, 'vehicle_doc');

        $multipleImages = $this->formatImages($vehicleImages, $vehicle->vehicle_image);
        $filteredPrices = $this->filterVehiclePrices($vehicle->vehicle_price);

        $authId = currentUser()?->id;
        $wishlistExists = $authId ? Wishlist::where('user_id', $authId)->where('vehicle_id', $vehicle->id)->exists() : false;

        $faqEnabled = $this->getGeneralSetting(20, 'faq');
        $extraServiceEnabled = $this->getGeneralSetting(20, 'extraService');
        $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;

        return [
            'id'                      => $vehicle->id,
            'name'                    => $vehicle->name,
            'slug'                    => $vehicle->slug,
            'vehicle_image'           => url(self::STORAGES . $vehicle->vehicle_image),
            'multiple_vehicle_doc'    => $this->urlizeArray($vehicleDocs),
            'multiple_vehicle_policy' => $this->urlizeArray($vehiclePolicies),
            'multiple_vehicle_images' => $multipleImages,
            'has_multiple_image'      => count($multipleImages) > 1,
            'brand'                   => $vehicle->brand->brand_name ?? null,
            'car_type'                => $vehicle->carType->name ?? null,
            'category'                => $vehicle->category->name ?? null,
            'location'                => $vehicle->mainLocation->name ?? null,
            'color'                   => $vehicle->color->name ?? null,
            'fuel_type'               => $vehicle->fuel_type->fuel_type ?? null,
            'transmission'            => $vehicle->transmission->name ?? null,
            'wishlist'                => $wishlistExists,
            'year'                    => $vehicle->year,
            'mileage'                 => $vehicle->mileage,
            'vin'                     => $vehicle->vin,
            'rating'                  => $rating,
            'passenger_capacity'      => $vehicle->passenger_capacity,
            'hatch'                   => $vehicle->hatch,
            'num_seats'               => $vehicle->num_seats,
            'num_doors'               => $vehicle->num_doors,
            'num_airbags'             => $vehicle->num_airbags,
            'vehicle_video'           => $vehicle->vehicle_video,
            'price'                   => !empty($filteredPrices) ? $filteredPrices : null,
            'created_at'              => $vehicle->created_at,
            'features'                => $features,
            'currency'                => getDefaultCurrencySymbol(),
            'seo_title'               => $vehicle->vehicle_metatitle,
            'seo_key'                 => $vehicle->vehicle_metakeywords,
            'seo_description'         => $vehicle->vehicle_metadesc,
            'is_featured'             => $vehicle->feature ?? 0,
            'is_top_rated'            => is_numeric($rating) && $rating >= 4,
            'authenticated'           => Auth::guard('web')->check(),
            'description'             => $vehicle->description,
            'extraservice'            => $extraServiceEnabled ? $this->formatExtraServices($vehicle->extraservices) : null,
            'tariff'                  => $vehicle->tariffs->map(fn($t) => $t->only(['tariff_title','tariff_daily_price','tariff_from_days','tariff_to_days','tariff_base_km','tariff_extra_price'])),
            'seasonal'                => $vehicle->seasonals->map(fn($s) => $s->only([
                'seasonal_title','seasonal_start_date','seasonal_end_date','seasonal_daily_rate','seasonal_weekly_rate','seasonal_monthly_rate','seasonal_late_fee'
            ])),
            'faqs'                    => $faqEnabled ? $vehicle->faqs->map(fn($f) => $f->only(['question','answer'])) : [],
            'damages'                 => $vehicle->damages->map(fn($d) => $d->only(['damage_type','damage_loaction','image','description'])),
            'owner_details'           => $this->formatOwner($vehicle)
        ];
    }

    private function getVehicleMeta(int $vehicleId, string $key): array
    {
        $meta = VehicleMeta::where('vehicle_id', $vehicleId)->where('key', $key)->first();
        return $meta ? json_decode($meta->value, true) : [];
    }

    private function filterVehiclePrices(?string $vehiclePrice): array
    {
        $filteredPrices = [];
        $prices = json_decode($vehiclePrice ?? '', true);

        foreach ($prices ?? [] as $price) {
            foreach ($price as $key => $value) {
                if ($value > 0) {
                    $filteredPrices[] = [$key => $value];
                }
            }
        }

        return $filteredPrices;
    }


    private function formatImages(array $images, ?string $mainImage): array
    {
        if (!empty($mainImage)) {
            array_unshift($images, $mainImage);
        }

        return array_map(
            fn($img) => url('storage' . str_replace(
                self::VEHICLE_IMAGE,
                'vehicles/images/medium/',
                '/' . ltrim($img, '/')
            )),
            $images
        );
    }


    private function urlizeArray(array $arr): array
    {
        return array_map(fn($item) => url(self::STORAGE . $item), $arr);
    }

    private function getGeneralSetting(int $groupId, string $key): bool
    {
        return (bool) GeneralSetting::where('group_id', $groupId)->where('key', $key)->value('value');
    }

    private function formatExtraServices($extraservices): array
    {
        return $extraservices->map(fn($es) => [
            'extra_service_id' => $es->extra_service_id,
            'value'            => $es->value,
            'price'            => $es->price,
            'name'             => optional($es->extraService)->name,
            'icon'             => uploadedAsset(optional($es->extraService)->icon),
            'description'      => optional($es->extraService)->description,
            'image'            => url(self::STORAGES . optional($es->extraService)->image),
        ])->toArray();
    }

    private function formatOwner(VehicleInfo $vehicle): ?array
    {
        if (!$vehicle->owner) {
            return null;
        }
        return [
            'name'         => $vehicle->owner->name,
            'phone_number' => $vehicle->owner->mobile_number,
            'email'        => $vehicle->owner->email,
            'image'        => $vehicle->owner->userDetails ? url(self::STORAGES . $vehicle->owner->userDetails->profile_image) : null
        ];
    }

    private function errorResponse(string $message, int $code): array
    {
        return [
            'code'    => $code,
            'success' => false,
            'message' => $message,
            'data'    => []
        ];
    }

    public function getVehicleList(): array
    {
        $vehicles = VehicleInfo::orderBy('id', 'desc')->get();
        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $vehicles
        ];
    }

    public function deleteVehicleImage(Request $request): array
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicle_metas,vehicle_id',
            'image_path' => 'required|string',
        ]);

        $response = [
            'code'    => 500,
            'success' => false,
            'message' => __('admin.common.default_delete_error')
        ];

        try {
            $vehicleMeta = VehicleMeta::where('vehicle_id', $request->vehicle_id)
                ->where('key', 'vehicle_image')
                ->first();

            if (!$vehicleMeta) {
                $response['code'] = 404;
                $response['message'] = 'Vehicle images not found.';
                return $response;
            }

            $images = json_decode($vehicleMeta->value, true);

            // Extract relative path from full URL if needed
            $imageToDelete = parse_url($request->image_path, PHP_URL_PATH);
            $relativePath = is_string($imageToDelete) ? ltrim(str_replace(self::STORAGES, '', $imageToDelete), '/') : null;

            // Find and remove image
            if (($key = array_search($relativePath, $images)) !== false) {
                unset($images[$key]);
                if ($relativePath) {
                    Storage::delete($relativePath);
                }
                $vehicleMeta->value = json_encode(array_values($images)) ?: '';

                $vehicleMeta->save();

                $response['code'] = 200;
                $response['success'] = true;
                $response['message'] = 'Image deleted successfully.';
            } else {
                $response['code'] = 404;
                $response['message'] = 'Image not found in database.';
            }
        } catch (\Exception $e) {
            // Already default response is 500 with default message
        }

        return $response;
    }


    public function deleteVehiclePolicy(Request $request): array
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicle_metas,vehicle_id',
            'file_path'  => 'required|string',
        ]);

        $response = [
            'code'    => 500,
            'success' => false,
            'message' => __('admin.common.default_delete_error')
        ];

        try {
            // Ensure the correct path format
            $filePath = 'vehicles/policy/' . $request->file_path;

            $vehicleMeta = VehicleMeta::where('vehicle_id', $request->vehicle_id)
                ->where('key', 'vehicle_policy')
                ->first();

            if (!$vehicleMeta) {
                $response = [
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Policy files not found.'
                ];
            } else {
                $policyFiles = json_decode($vehicleMeta->value, true);

                // Find and remove the file from the array
                if (($key = array_search($filePath, $policyFiles)) !== false) {
                    unset($policyFiles[$key]);
                    Storage::delete($filePath); // Delete from storage
                    $vehicleMeta->value = json_encode(array_values($policyFiles)) ?: '';
                    $vehicleMeta->save();

                    $response = [
                        'code'    => 200,
                        'success' => true,
                        'message' => 'Policy file deleted successfully.'
                    ];
                } else {
                    $response = [
                        'code'    => 404,
                        'success' => false,
                        'message' => 'Policy file not found.'
                    ];
                }
            }
        } catch (\Exception $e) {
            // $response is already initialized with a default error message
        }

        return $response;
    }

    public function vehicleInterestLists(Request $request): array
    {
        $lang_id = $this->getLangId();
        $vehicles = $this->getVehicles($lang_id, $request->category_id);

        $data = $vehicles->map(fn($vehicle) => $this->formatVehiclesData($vehicle));

        $html = view('frontend.home.list.recommended-vehicles', compact('data'))->render();

        return [
            'code'    => 200,
            'message' => __('Vehicles retrieved successfully.'),
            'html'    => $html
        ];
    }

    /**
     * Get current language id
     */
    private function getLangId(): int
    {
        $authUser = currentUser();
        if ($authUser && !empty($authUser->language_id)) {
            return $authUser->language_id;
        }

        $currentLocale = App::getLocale();
        if ($currentLocale) {
            $language = TranslationLanguage::where('code', $currentLocale)->first();
            return $language->id ?? 1;
        }

        $defaultLang = Language::select("language_id")->where("default", 1)->first();
        return $defaultLang->language_id ?? 1;
    }

    /**
     * Retrieve vehicles with related data
     */
    private function getVehicles(?int $lang_id, ?int $categoryId)
    {
        return VehicleInfo::with([
            self::CAR_TYPE,
            self::BRAND,
            self::CATEGORY,
            self::MAIN_LOCATION,
            self::COLOR,
            self::FUEL_TYPE,
            self::TRANSMISSION,
            'reviews:id,vehicle_id,average_ratings'
        ])
        ->where("language_id", $lang_id)
        ->where('category_id', $categoryId)
        ->take(6)
        ->get();
    }

    /**
     * Format single vehicle data
     */
    private function formatVehiclesData($vehicle): array
    {
        $multipleImages = $this->getVehicleImages($vehicle);
        $filteredPrices = $this->getFilteredPrices($vehicle->vehicle_price);
        $wishlistExists = $this->checkWishlist($vehicle->id);
        $currencySymbol = $this->getCurrencySymbols();
        $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;
        $review_count = Review::where("vehicle_id", $vehicle->id)->count();
        $avatarImage = $this->getOwnerAvatar($vehicle);

        return [
            'id'                 => $vehicle->id,
            'name'               => $vehicle->name,
            'slug'               => $vehicle->slug,
            'vehicle_image'      => $multipleImages[0] ?? null,
            'avatar_image'       => $avatarImage,
            'brand'              => $vehicle->brand->brand_name ?? null,
            'car_type'           => $vehicle->carType->name ?? null,
            'category'           => $vehicle->category->name ?? null,
            'location'           => $vehicle->mainLocation->name ?? null,
            'color'              => $vehicle->color->name ?? null,
            'fuel_type'          => $vehicle->fuel_type->fuel_type ?? null,
            'transmission'       => $vehicle->transmission->name ?? null,
            'year'               => $vehicle->year,
            'mileage'            => $vehicle->mileage,
            'passenger_capacity' => $vehicle->passenger_capacity,
            'num_seats'          => $vehicle->num_seats,
            'num_doors'          => $vehicle->num_doors,
            'num_airbags'        => $vehicle->num_airbags,
            'vehicle_video'      => $vehicle->vehicle_video,
            'features'           => $vehicle->features,
            'currency'           => $currencySymbol,
            'rating'             => $rating,
            'wishlist'           => $wishlistExists,
            'review_count'       => $review_count,
            'price'              => !empty($filteredPrices) ? $filteredPrices : null,
            'is_featured'        => $vehicle->feature ?? 0,
            'is_top_rated'       => is_numeric($rating) && $rating >= 4,
            'seo_title'          => $vehicle->vehicle_metatitle,
            'seo_key'            => $vehicle->vehicle_metakeywords,
            'seo_description'    => $vehicle->vehicle_metadesc,
            'authenticated'      => Auth::guard('web')->check(),
            'created_at'         => $vehicle->created_at,
            'status'             => $vehicle->status,
        ];
    }

    /**
     * Get vehicle images
     */
    private function getVehicleImages($vehicle): array
    {
        $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
            ->where('key', 'vehicle_image')
            ->first();

        $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

        if (!empty($vehicle->vehicle_image)) {
            array_unshift($multipleImages, $vehicle->vehicle_image);
        }

        return array_map(fn($img) => url('storage' . str_replace(self::VEHICLE_IMAGE, self::VEHICLE_IMAGE_SMALL, '/' . ltrim($img, '/'))), $multipleImages);
    }

    /**
     * Filter vehicle prices
     */
    private function getFilteredPrices($vehiclePriceJson): array
    {
        $vehiclePrices = json_decode((string) $vehiclePriceJson, true) ?? [];
        $filteredPrices = [];

        foreach ($vehiclePrices as $price) {
            foreach ($price as $key => $value) {
                if ($value > 0) {
                    $filteredPrices[] = [$key => $value];
                }
            }
        }

        return $filteredPrices;
    }

    /**
     * Check wishlist
     */
    private function checkWishlist(int $vehicleId): bool
    {
        $authId = currentUser()->id ?? null;
        if (!$authId) {
            return false;
        }

        return Wishlist::where("user_id", $authId)
            ->where("vehicle_id", $vehicleId)
            ->exists();
    }


    /**
     * Get currency symbol
     */
    private function getCurrencySymbols(): string
    {
        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        $currency = $currencySetting && $currencySetting->value ? Currency::find($currencySetting->value) : null;

        return $currency->symbol ?? "$";
    }

    /**
     * Get owner avatar
     */
    private function getOwnerAvatar($vehicle): string
    {
        $defaultAvatar = asset('/backend/assets/img/default-profile.png');
        $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;

        return $profileImagePath ? uploadedAsset($profileImagePath, 'profile') : $defaultAvatar;
    }

    public function getDamageDetails(Request $request): array
    {
        $id = $request->id;
        $damage = VehicleDamage::find($id);

        return [
            'code'    => 200,
            'success' => true,
            'data'    => $damage
        ];
    }

    public function delete(int|array $id): array
    {
        try {
            if (is_array($id) && !empty($id)) {
                VehicleInfo::whereIn('id', $id)->delete();
            } else {
                /** @var \Modules\CarInfo\Models\VehicleInfo $vehicle */
                $vehicle = VehicleInfo::findOrFail($id);
                $vehicle->delete();
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.vehicle_delete_success'),
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }

    public function setPopular(Request $request)
    {
        try {
            /** @var VehicleInfo $vehicle */
            $vehicle = VehicleInfo::find($request->id);

            if (!$vehicle) {
                return [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->popular = $request->popular ? 1 : 0;
            $vehicle->save();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.popular_status_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ];
        }
    }

    public function setRecommended(Request $request)
    {
        try {
            /** @var VehicleInfo $vehicle */
            $vehicle = VehicleInfo::find($request->id);

            if (!$vehicle) {
                return [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->recommended = $request->recommended ? 1 : 0;
            $vehicle->save();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.recommended_status_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ];
        }
    }

    public function setStatus(Request $request)
    {
        try {
            /** @var VehicleInfo $vehicle */
            $vehicle = VehicleInfo::find($request->vehicle_id);

            if (!$vehicle) {
                return [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->status = $request->status ? 1 : 0;
            $vehicle->save();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.common.default_status_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_status_error'),
            ];
        }
    }
}
