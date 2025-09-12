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

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function index(): array
    {
        $langID = current_user()->language_id ?? 1;

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
        $authUser = current_user();
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

        $authUser = current_user();

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
        if ($request->has('language_id')) {
            $language_id = $request->query('language_id');
            /** @var \Modules\GeneralSetting\Models\Language */
            $language = Language::find($language_id);
            $languageId = $language->language_id;
        } else {
            $authId = current_user();
            $language_id = $authId->language_id ?? null;
            $language = $language_id;
            $languageId = $language;
        }

        $languageCode = $languageId ? TranslationLanguage::find($languageId) : null;

        $query = VehicleInfo::select(
            "id",
            "language_id",
            "parent_id",
            "vehicle_image",
            "name",
            "slug",
            "type_id",
            "brand_id",
            "model_id",
            "category_id",
            "plate_number",
            "perma_link",
            "vin",
            "main_location_id",
            "other_location_id",
            "fuel_type_id",
            "odometer",
            "color_id",
            "year",
            "transmission_id",
            "mileage",
            "passenger_capacity",
            "water_tight",
            "sliding",
            "hatch",
            "num_seats",
            "num_doors",
            "num_airbags",
            "features",
            "vehicle_basekm",
            "vehicle_extrakmprice",
            "vehicle_price",
            'vehicle_metatitle',
            'vehicle_metadesc',
            'vehicle_metakeywords',
            'description',
            'vehicle_video',
        )->where("slug", $slug)->when($languageId, function ($q) use ($languageId) {
            $q->where('language_id', $languageId);
        })->first();

        if (!$query && $languageId) {
            $baseVehicle = VehicleInfo::where('slug', $slug)->whereNull('parent_id')->first();
            if ($baseVehicle) {
                $query = VehicleInfo::where('parent_id', $baseVehicle->id)
                    ->where('language_id', $languageId)
                    ->first();

                if (!$query) {
                    $query = new VehicleInfo([
                        'language_id' => $languageId,
                        'parent_id'   => $baseVehicle->id,
                    ]);
                }
            }
        }

        if (!$query) {
            $baseVehicle = VehicleInfo::where('slug', $slug)->first();

            if ($baseVehicle != null) {
                $query = VehicleInfo::where('id', $baseVehicle->parent_id)
                    ->where('language_id', $languageId)
                    ->first();

                if ($query == null) {
                    $query = new VehicleInfo([
                        'language_id' => $languageId,
                        'parent_id'   => $baseVehicle->parent_id,
                    ]);
                }
            }
        }

        $selectedFeatures = [];
        if ($query && $query->features) {
            $selectedFeatures = json_decode($query->features, true);
        }

        $vehiclePrices = [];
        if ($query && $query->vehicle_price) {
            $vehiclePrices = json_decode($query->vehicle_price, true)[0] ?? [];
        }

        $carTypes = Cartype::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $brands = Brand::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $models = collect();

        $category = Category::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $location = Location::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $carFuel = CarFuel::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $carColor = CarColor::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $transmission = Transmission::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $safetyFeature = SafetyFeature::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $damageTypes = DamageType::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();

        $extraServices = ExtraService::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();

        $extraServiceInfo = VehicleExtraService::where('vehicle_id', $query->id ?? null)
            ->whereIn('extra_service_id', $extraServices->pluck('id'))
            ->get();

        $authId = current_user()->language_id;
        $insurances = Insurance::with('insuranceBenefits', 'priceType')
            ->where('language_id', $authId)
            ->where('status', 1)
            ->get();

        $priceType = PricingType::where('status', 1)->get();

        app()->setLocale($languageCode->code ?? 'en');

        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        $currency = null;

        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
        }

        $currencySymbol = $currency->symbol ?? "$";

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
            'query'            => $query,
            'currencySymbol'   => $currencySymbol,
        ];

    }

    public function createVehicleInfo(Request $request)
    {
        try {
            $authId = Auth::guard('admin')->id();

            $vehiclePrice = [];

            if ($request->has('daily_price')) {
                $vehiclePrice['daily'] = $request->daily_price;
            }

            if ($request->has('weekly_price')) {
                $vehiclePrice['weekly'] = $request->weekly_price;
            }

            if ($request->has('montly_price')) {
                $vehiclePrice['monthly'] = $request->montly_price;
            }

            if ($request->has('yearly_price')) {
                $vehiclePrice['yearly'] = $request->yearly_price;
            }

            $vehiclePriceJson = json_encode([$vehiclePrice]);

            $slug = Str::slug($request->title);
            $vehicleImagePath = null;
            if ($request->hasFile('vehicle_image')) {
                $file = $request->file('vehicle_image');
                if ($file && $file->isValid()) {
                    $vehicleImagePath = $this->imageResizer->uploadFile($file, self::VEHICLE_IMAGE_PATH);
                }
            }

            $baseKilo = ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('basic_kilometer', null);
            $extraKilo = ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('extra_kilometer', null);

            $category = Category::find($request->vehicle_category_id);

            $data = [
                "vehicle_image"        => $vehicleImagePath,
                "language_id"          => $request->lang_id,
                "name"                 => $request->title,
                'slug'                 => $slug,
                "perma_link"           => $request->perma_link,
                "type_id"              => $request->vehicle_type_id,
                "brand_id"             => $request->vehicle_brand_id,
                "model_id"             => $request->vehicle_model_id,
                "category_id"          => $request->vehicle_category_id,
                "type"                 => $category?->slug ?? null,
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

            $save = VehicleInfo::create($data);

            if ($request->hasFile('car_images')) {
                /** @var UploadedFile[]|UploadedFile|null $images */
                $images = $request->file('car_images');
                $imagePaths = [];
                if (is_array($images)) {
                    foreach ($images as $image) {
                        $fileName = $this->imageResizer->uploadFile($image, self::VEHICLE_IMAGE_PATH);
                        $imagePaths[] = $fileName;
                    }
                }

                if (!empty($imagePaths)) {
                    VehicleMeta::create([
                        'vehicle_id' => $save->id,
                        'key'        => 'vehicle_image',
                        'value'      => json_encode($imagePaths),
                    ]);
                }
            }

            // Handle car documents
            if ($request->hasFile('car_document')) {
                /** @var UploadedFile[]|UploadedFile|null $carDocs */
                $carDocs = $request->file('car_document');
                $carDocPaths = [];
                if (is_array($carDocs)) {
                    foreach ($carDocs as $doc) {
                        $fileName = uploadFile($doc, 'vehicles/document');
                        $carDocPaths[] = $fileName;
                    }
                }

                if (!empty($carDocPaths)) {
                    VehicleMeta::create([
                        'vehicle_id' => $save->id,
                        'key'        => 'vehicle_doc',
                        'value'      => json_encode($carDocPaths),
                    ]);
                }
            }

            if ($request->hasFile('policy_document')) {
                /** @var UploadedFile[]|UploadedFile|null $policyDocs */
                $policyDocs = $request->file('policy_document');
                $policyDocPaths = [];
                if (is_array($policyDocs)) {
                    foreach ($policyDocs as $doc) {
                        $fileName = uploadFile($doc, 'vehicles/policy');
                        $policyDocPaths[] = $fileName;
                    }
                }

                if (!empty($policyDocPaths)) {
                    VehicleMeta::create([
                        'vehicle_id' => $save->id,
                        'key'        => 'vehicle_policy',
                        'value'      => json_encode($policyDocPaths),
                    ]);
                }
            }

            if ($request->has('vehicle_faq')) {
                $vehicleFaqs = json_decode($request->input('vehicle_faq'), true);

                if (is_array($vehicleFaqs)) {
                    foreach ($vehicleFaqs as $faq) {
                        if (!empty($faq['id'])) {
                            VehicleFaq::where('id', $faq['id'])
                                ->where('vehicle_id', $save->id)
                                ->update([
                                    'question' => $faq['question'],
                                    'answer'   => $faq['answer'],
                                ]);
                        } else {
                            VehicleFaq::create([
                                'vehicle_id' => $save->id,
                                'question'   => $faq['question'],
                                'answer'     => $faq['answer'],
                            ]);
                        }
                    }
                }
            }

            if ($request->has('vehicle_insurance')) {
                $vehicleInsurances = json_decode($request->input('vehicle_insurance'), true);

                if (is_array($vehicleInsurances)) {
                    foreach ($vehicleInsurances as $insurance) {
                        if (!empty($insurance['id']) && !empty($insurance['price']) && !empty($insurance['type'])) {
                            // Normalize type to 'Percentage' or 'Fixed'
                            $type = in_array(strtolower($insurance['type']), ['%', 'percentage']) ? 'Percentage' : 'Fixed';

                            VehicleInsurance::create([
                                'vehicle_id'    => $save->id,
                                'insurances_id' => $insurance['id'],     // Insurance ID
                                'value'         => $type,                // Type as normalized value
                                'price'         => $insurance['price'],  // Raw price value
                            ]);
                        }
                    }
                }
            }

            if ($request->has('tariff')) {
                $tariffs = json_decode($request->input('tariff'), true);

                if (is_array($tariffs)) {
                    foreach ($tariffs as $tariff) {
                        if (!empty($tariff['id'])) {
                            VehicleTarrif::where('id', $tariff['id'])
                                ->where('vehicle_id', $save->id)
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
                                'vehicle_id'         => $save->id,
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
            }

            if ($request->has('seasonal')) {
                $seasonals = json_decode($request->input('seasonal'), true);

                if (is_array($seasonals)) {
                    foreach ($seasonals as $season) {
                        if (!empty($season['id'])) {
                            VehicleSeason::where('id', $season['id'])
                                ->where('vehicle_id', $save->id)
                                ->update([
                                    'seasonal_title'        => $season['title'],
                                    'seasonal_start_date'   => $season['start_date'],
                                    'seasonal_end_date'     => $season['end_date'],
                                    'seasonal_daily_rate'   => $season['daily_rate'],
                                    'seasonal_weekly_rate'  => $season['weekly_rate'],
                                    'seasonal_monthly_rate' => $season['monthly_rate'],
                                    'seasonal_late_fee'     => $season['late_fee'],
                                ]);
                        } else {
                            // Create new seasonal pricing if ID is null
                            VehicleSeason::create([
                                'vehicle_id'            => $save->id,
                                'seasonal_title'        => $season['title'],
                                'seasonal_start_date'   => $season['start_date'],
                                'seasonal_end_date'     => $season['end_date'],
                                'seasonal_daily_rate'   => $season['daily_rate'],
                                'seasonal_weekly_rate'  => $season['weekly_rate'],
                                'seasonal_monthly_rate' => $season['monthly_rate'],
                                'seasonal_late_fee'     => $season['late_fee'],
                            ]);
                        }
                    }
                }
            }

            if ($request->has('extra_services')) {
                $extraServices = json_decode($request->input('extra_services'), true);

                if (is_array($extraServices)) {
                    foreach ($extraServices as $service) {
                        $serviceId = $service['service_id'];  // Input service ID
                        $value = $service['value'];           // Service value
                        $price = $service['price'];           // Service price

                        $existingService = VehicleExtraService::where('vehicle_id', $save->id)
                            ->where('extra_service_id', $serviceId)
                            ->first();

                        if ($existingService) {
                            $existingService->update([
                                'value' => $value,
                                'price' => $price,
                            ]);
                        } else {
                            VehicleExtraService::create([
                                'vehicle_id'       => $save->id,
                                'extra_service_id' => $serviceId,
                                'value'            => $value,
                                'price'            => $price,
                            ]);
                        }
                    }
                }
            }

            if ($request->has('vehicle_damage')) {
                /** @var array<int, array<string, mixed>>|null $vehicleDamages */
                $vehicleDamages = json_decode($request->input('vehicle_damage'), true);

                if (is_array($vehicleDamages)) {
                    foreach ($vehicleDamages as $index => $damage) {
                        $damageImages = $request->allFiles()['damage_image'] ?? null;

                        $imageFile = null;
                        if (is_array($damageImages)) {
                            $imageFile = $damageImages[$index] ?? null;
                        } elseif ($index === 0 && $damageImages instanceof \Illuminate\Http\UploadedFile) {
                            $imageFile = $damageImages;
                        }

                        $uploadedImage = $damage['image'] ?? null;
                        if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
                            $uploadedImage = $imageFile->store('vehicles/damage', 'public');
                        } elseif (!empty($uploadedImage) && strpos($uploadedImage, 'data:image') === 0) {
                            $imageData = explode(',', $uploadedImage)[1];
                            $imageName = 'vehicles/damage/' . uniqid() . '.png';
                            Storage::disk('public')->put($imageName, base64_decode($imageData));
                            $uploadedImage = $imageName;
                        }

                        if (!empty($damage['id'])) {
                            VehicleDamage::where('id', $damage['id'])
                                ->where('vehicle_id', $save->id)
                                ->update([
                                    'damage_type'     => $damage['name'],
                                    'damage_loaction' => $damage['location'],
                                    'image'           => $uploadedImage,
                                    'description'     => $damage['description'],
                                ]);
                        } else {
                            VehicleDamage::create([
                                'vehicle_id'      => $save->id,
                                'damage_type'     => $damage['name'],
                                'damage_loaction' => $damage['location'],
                                'image'           => $uploadedImage,
                                'description'     => $damage['description'],
                            ]);
                        }
                    }
                }
            }

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

    public function updateVehicleInfo(Request $request): array
    {
        try {
            $authId = Auth::guard('admin')->id();

            $vehicleID = $request->vehicle_id;
            /** @var \Modules\CarInfo\Models\VehicleInfo $vehicle */
            $vehicle = VehicleInfo::find($vehicleID);

            $vehiclePrice = [];

            if ($request->has('daily_price')) {
                $vehiclePrice['daily'] = $request->daily_price;
            }

            if ($request->has('weekly_price')) {
                $vehiclePrice['weekly'] = $request->weekly_price;
            }

            if ($request->has('monthly_price')) {
                $vehiclePrice['monthly'] = $request->monthly_price;
            }

            if ($request->has('yearly_price')) {
                $vehiclePrice['yearly'] = $request->yearly_price;
            }

            $vehiclePriceJson = json_encode([$vehiclePrice]);

            $slug = Str::slug($request->title);
            $vehicleImagePath = null;
            if ($request->hasFile('vehicle_image')) {
                $file = $request->file('vehicle_image');
                $existingImage = $vehicle->vehicle_image ?? null;
                if ($file && $file->isValid()) {
                    $vehicleImagePath = $this->imageResizer->uploadFile($file, self::VEHICLE_IMAGE_PATH, $existingImage);
                }
            } else {
                $vehicleImagePath = $vehicle->vehicle_image;
            }

            $baseKilo = ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('basic_kilometer', null);
            $extraKilo = ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('extra_kilometer', null);

            $category = Category::find($request->vehicle_category_id);

            $data = [
                "vehicle_image"        => $vehicleImagePath,
                "parent_id"            => (int) $request->parent_id,
                "name"                 => $request->title,
                'slug'                 => $slug,
                "perma_link"           => $request->perma_link,
                "type_id"              => $request->vehicle_type_id,
                "brand_id"             => $request->vehicle_brand_id,
                "model_id"             => $request->vehicle_model_id,
                "category_id"          => $request->vehicle_category_id,
                "type"                 => $category?->slug ?? null,
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

            if ($request->filled('vehicle_id')) {
                $update = VehicleInfo::where('id', $request->vehicle_id)->firstOrFail();

                $update->update($data);
            } else {
                $data['language_id'] = (int) $request->language_id;
                $update = VehicleInfo::create($data);
            }

            if ($request->hasFile('car_images')) {
                /** @var UploadedFile[]|UploadedFile|null $images */
                $images = $request->file('car_images');
                $imagePaths = [];
                if (is_array($images)) {
                    foreach ($images as $image) {
                        $fileName = $this->imageResizer->uploadFile($image, self::VEHICLE_IMAGE_PATH);
                        $imagePaths[] = $fileName;
                    }
                }

                $vehicleMeta = VehicleMeta::where('vehicle_id', $update->id)
                    ->where('key', 'vehicle_image')
                    ->first();

                if ($vehicleMeta) {
                    $existingImages = json_decode($vehicleMeta->value, true) ?? [];
                    $updatedImages = array_merge($existingImages, $imagePaths);

                    $vehicleMeta->update([
                        'value' => json_encode($updatedImages),
                    ]);
                } else {
                    VehicleMeta::create([
                        'vehicle_id' => $update->id,
                        'key'        => 'vehicle_image',
                        'value'      => json_encode($imagePaths),
                    ]);
                }
            }

            if ($request->hasFile('policy_document')) {
                /** @var UploadedFile[]|UploadedFile|null $policyDocs */
                $policyDocs = $request->file('policy_document');
                $policyDocPaths = [];
                if (is_array($policyDocs)) {
                    foreach ($policyDocs as $doc) {
                        $fileName = uploadFile($doc, 'vehicles/policy');
                        $policyDocPaths[] = $fileName;
                    }
                }

                // Retrieve existing policy documents
                $vehicleMeta = VehicleMeta::where('vehicle_id', $update->id)
                    ->where('key', 'vehicle_policy')
                    ->first();

                if ($vehicleMeta) {
                    $existingDocs = json_decode($vehicleMeta->value, true) ?? [];
                    $updatedDocs = array_merge($existingDocs, $policyDocPaths); // Append new documents

                    $vehicleMeta->update([
                        'value' => json_encode($updatedDocs),
                    ]);
                } else {
                    VehicleMeta::create([
                        'vehicle_id' => $update->id,
                        'key'        => 'vehicle_policy',
                        'value'      => json_encode($policyDocPaths),
                    ]);
                }
            }

            if ($request->hasFile('car_document')) {
                /** @var UploadedFile[]|UploadedFile|null $vehicleDocs */
                $vehicleDocs = $request->file('car_document');
                $vehicleDocsPaths = [];
                if (is_array($vehicleDocs)) {
                    foreach ($vehicleDocs as $doc) {
                        $fileName = uploadFile($doc, 'vehicles/document');
                        $vehicleDocsPaths[] = $fileName;
                    }
                }

                // Retrieve existing policy documents
                $vehicleMeta = VehicleMeta::where('vehicle_id', $update->id)
                    ->where('key', 'vehicle_doc')
                    ->first();

                if ($vehicleMeta) {
                    $existingDocs = json_decode($vehicleMeta->value, true) ?? [];
                    $updatedDocs = array_merge($existingDocs, $vehicleDocsPaths);

                    $vehicleMeta->update([
                        'value' => json_encode($updatedDocs),
                    ]);
                } else {
                    VehicleMeta::create([
                        'vehicle_id' => $update->id,
                        'key'        => 'vehicle_doc',
                        'value'      => json_encode($vehicleDocsPaths),
                    ]);
                }
            }

            if ($request->has('vehicle_faq')) {
                $vehicleFaqs = json_decode($request->input('vehicle_faq'), true);

                if (is_array($vehicleFaqs)) {
                    // Delete existing FAQs for this vehicle
                    VehicleFaq::where('vehicle_id', $update->id)->delete();

                    // Insert new FAQs
                    foreach ($vehicleFaqs as $faq) {
                        VehicleFaq::create([
                            'vehicle_id' => $update->id,
                            'question'   => $faq['question'],
                            'answer'     => $faq['answer'],
                        ]);
                    }
                }
            }

            if ($request->has('vehicle_insurance')) {
                $vehicleInsurances = json_decode($request->input('vehicle_insurance'), true);

                if (is_array($vehicleInsurances)) {
                    VehicleInsurance::where('vehicle_id', $update->id)->delete();

                    foreach ($vehicleInsurances as $insurance) {
                        if (!empty($insurance['id']) && !empty($insurance['price']) && !empty($insurance['type'])) {
                            $type = strtolower($insurance['type']) === '%' || strtolower($insurance['type']) === 'percentage'
                                ? 'Percentage'
                                : 'Fixed';

                            VehicleInsurance::create([
                                'vehicle_id'    => $update->id,
                                'insurances_id' => $insurance['id'],
                                'value'         => $type,
                                'price'         => $insurance['price'],
                            ]);
                        }
                    }
                }
            }

            if ($request->has('tariff')) {
                $tariffs = json_decode($request->input('tariff'), true);

                if (is_array($tariffs)) {
                    foreach ($tariffs as $tariff) {
                        if (!empty($tariff['id'])) {
                            VehicleTarrif::where('id', $tariff['id'])
                                ->where('vehicle_id', $update->id)
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
                                'vehicle_id'         => $update->id,
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
            }

            if ($request->has('seasonal')) {
                $seasonals = json_decode($request->input('seasonal'), true);

                if (is_array($seasonals)) {
                    foreach ($seasonals as $season) {
                        if (!empty($season['id'])) {
                            // Update existing seasonal price
                            VehicleSeason::where('id', $season['id'])
                                ->where('vehicle_id', $update->id)
                                ->update([
                                    'seasonal_title'        => $season['title'],
                                    'seasonal_start_date'   => $season['start_date'],
                                    'seasonal_end_date'     => $season['end_date'],
                                    'seasonal_daily_rate'   => $season['daily_rate'],
                                    'seasonal_weekly_rate'  => $season['weekly_rate'],
                                    'seasonal_monthly_rate' => $season['monthly_rate'],
                                    'seasonal_late_fee'     => $season['late_fee'],
                                ]);
                        } else {
                            // Create new seasonal pricing if ID is null
                            VehicleSeason::create([
                                'vehicle_id'            => $update->id,
                                'seasonal_title'        => $season['title'],
                                'seasonal_start_date'   => $season['start_date'],
                                'seasonal_end_date'     => $season['end_date'],
                                'seasonal_daily_rate'   => $season['daily_rate'],
                                'seasonal_weekly_rate'  => $season['weekly_rate'],
                                'seasonal_monthly_rate' => $season['monthly_rate'],
                                'seasonal_late_fee'     => $season['late_fee'],
                            ]);
                        }
                    }
                }
            }

            if ($request->has('extra_services')) {
                $extraServices = json_decode($request->input('extra_services'), true);

                if (is_array($extraServices)) {
                    // Delete all existing extra services for this update
                    VehicleExtraService::where('vehicle_id', $update->id)->delete();

                    // Insert new records
                    foreach ($extraServices as $service) {
                        VehicleExtraService::create([
                            'vehicle_id'       => $update->id,
                            'extra_service_id' => $service['service_id'],
                            'value'            => $service['value'],
                            'price'            => $service['price'],
                        ]);
                    }
                }
            }

            if ($request->has('vehicle_damage')) {
                /** @var array<int, array<string, mixed>>|null $vehicleDamages */
                $vehicleDamages = json_decode($request->input('vehicle_damage'), true);

                if (is_array($vehicleDamages)) {
                    foreach ($vehicleDamages as $index => $damage) {
                        $damageImages = $request->allFiles()['damage_image'] ?? null;

                        $imageFile = null;
                        if (is_array($damageImages)) {
                            $imageFile = $damageImages[$index] ?? null;
                        } elseif ($index === 0 && $damageImages instanceof \Illuminate\Http\UploadedFile) {
                            $imageFile = $damageImages;
                        }
                        $uploadedImage = $damage['image'] ?? null;

                        if ($imageFile) {
                            $uploadedImage = $imageFile->store('vehicles/damages', 'public');
                        } elseif (!empty($uploadedImage) && strpos($uploadedImage, 'data:image') === 0) {
                            $imageData = explode(',', $uploadedImage)[1];
                            $imageName = 'vehicles/damages/' . uniqid() . '.png';
                            Storage::disk('public')->put($imageName, base64_decode($imageData));
                            $uploadedImage = $imageName;
                        }

                        if (!empty($damage['id'])) {
                            VehicleDamage::where('id', $damage['id'])
                                ->where('vehicle_id', $update->id)
                                ->update([
                                    'damage_type'     => $damage['name'],
                                    'damage_loaction' => $damage['location'],
                                    'image'           => $uploadedImage,
                                    'description'     => $damage['description'],
                                ]);
                        } else {
                            VehicleDamage::create([
                                'vehicle_id'      => $update->id,
                                'damage_type'     => $damage['name'],
                                'damage_loaction' => $damage['location'],
                                'image'           => $uploadedImage,
                                'description'     => $damage['description'],
                            ]);
                        }
                    }
                }
            }

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

    public function adminVehicleList(Request $request): array
    {
        $response = [
            'code'    => 500,
            'status'  => 'error',
            'message' => __('admin.common.default_retrieve_error'),
        ];

        try {
            /** @var \App\Models\User|null $authId */
            $authId = current_user();
            if (!$authId) {
                return [
                    'code'    => 401,
                    'message' => __('Unauthorized.'),
                ];
            }

            $languageId = $authId->language_id;
            $query = VehicleInfo::with([
                self::CAR_TYPE,
                self::BRAND,
                self::CATEGORY,
                self::MAIN_LOCATION,
                self::COLOR
            ])->select(
                "id",
                "vehicle_image",
                "name",
                "slug",
                "type_id",
                "brand_id",
                "category_id",
                "main_location_id",
                "color_id",
                "vehicle_price",
                "vehicle_basekm",
                "created_at",
                "perma_link",
                "model_id",
                "plate_number",
                "vin",
                "other_location_id",
                "fuel_type_id",
                "odometer",
                "year",
                "transmission_id",
                "mileage",
                "passenger_capacity",
                "num_seats",
                "num_doors",
                "num_airbags",
                "vehicle_video",
                "vehicle_extrakmprice",
                "vehicle_metatitle",
                "vehicle_metadesc",
                "vehicle_metakeywords",
                "features",
                "popular",
                "recommended",
                "status"
            );

            // Apply filters if provided
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

            // Sorting logic with default to ascending
            $sortBy = $request->sort_by ?? 'ascending';

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
                case 'last_month':
                    $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
                    break;
                case 'last_7_days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                default:
                    // Fallback to ascending order if an invalid sort option is passed
                    $query->orderBy('name', 'asc');
                    break;
            }

            if (!is_null($request->sort_by_date)) {
                $dates = explode(' - ', $request->sort_by_date);
                if (count($dates) === 2) {
                    try {
                        $stDate = $dates[0];
                        $enDate = $dates[1];

                        if ($stDate && $enDate) {
                            $startDate = Carbon::createFromFormat(self::DATE_FORMAT, trim($stDate));
                            $endDate = Carbon::createFromFormat(self::DATE_FORMAT, trim($enDate));

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

            $vehicles = $query->where("language_id", $languageId)->get()->map(function ($vehicle) {
                $vehicleImagePath = $vehicle->vehicle_image ?? '';
                $filename = basename($vehicleImagePath);
                $newpath = self::VEHICLE_IMAGE_SMALL . $filename;
                $file = public_path(self::STORAGE . $newpath);
                if (file_exists($file)) {
                    $vehicleImagePath = $newpath;
                }
                $vehicle->vehicle_image = uploadedAsset($vehicleImagePath);

                $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
                $currency = null;

                if ($currencySetting && $currencySetting->value) {
                    $currency = Currency::find($currencySetting->value);
                }

                $currencySymbol = $currency->symbol ?? "$";

                $vehicleMetas = VehicleMeta::where('vehicle_id', $vehicle->id)
                    ->where('key', 'vehicle_image')
                    ->first();

                if ($vehicleMetas) {
                    $images = $vehicleMetas->value ? json_decode($vehicleMetas->value) : [];
                    $vehicle->multiple_vehicle_images = array_map(fn ($img) => url('storage/vehicles/' . basename($img)), $images);
                } else {
                    $vehicle->multiple_vehicle_images = [];
                }
                $vehicle->has_multiple_image = count($vehicle->multiple_vehicle_images) > 1;

                $damageCount = VehicleDamage::where('vehicle_id', $vehicle->id)->count();
                $vehicle->damage_count = $damageCount;
                $vehicle->status = $vehicle->status;
                $vehicle->currency = $currencySymbol;
                $vehicle->created_date = formatDateTime($vehicle->created_at, false);

                return $vehicle;
            });

            $response = [
                'code'    => 200,
                'status'  => 'success',
                'message' => __('Vehicles list retrieved successfully.'),
                'data'    => $vehicles,
            ];
        } catch (\Exception $e) {
            // $response is already initialized with default error response
        }

        return $response;
    }

    public function vehicleLists(Request $request): array
    {
        $query = VehicleInfo::with([
            self::CAR_TYPE,
            self::BRAND,
            self::CATEGORY,
            self::MAIN_LOCATION,
            self::COLOR,
            'fuel_type:id,fuel_type',
            'transmission:id,name',
            'reviews:id,vehicle_id,average_ratings'
        ]);

        $authUser = current_user();

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
                    'message' => __('Vehicles list retrieved successfully.'),
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
            $auth = current_user();
            $authId = $auth->id ?? null;

            $wishlistExists = false;

            if ($authId) {
                $wishlistExists = Wishlist::where("user_id", $authId)
                    ->where("vehicle_id", $vehicle->id)
                    ->exists();
            }

            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
            $currency = null;

            if ($currencySetting && $currencySetting->value) {
                $currency = Currency::find($currencySetting->value);
            }

            $currencySymbol = $currency->symbol ?? "$";

            $user = User::where('id', $vehicle->created_by)->first();

            $userDetail = null;

            if ($user) {
                $userDetail = UserDetail::where("user_id", $user->id)->first();
                $userProfileImg = $userDetail && $userDetail->profile_image
                    ? url(self::STORAGES . $userDetail->profile_image)
                    : null;
            }


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
                'is_featured'             => $vehicle->popular == 1 ? true : false,
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
            'message'    => __('Vehicles list retrieved successfully.'),
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
                        \Log::warning("Unhandled document key: {$document->key}");
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
        try {
            $vehicleSlug = $request->vehicle_slug;

            if (!$vehicleSlug) {
                return [
                    'code'    => 400,
                    'success' => false,
                    'message' => 'Vehicle ID is required'
                ];
            }

            $query = VehicleInfo::with([
                self::CAR_TYPE,
                self::BRAND,
                self::CATEGORY,
                self::MAIN_LOCATION,
                self::COLOR,
                'fuel_type:id,fuel_type',
                'transmission:id,name',
                'extraservices.extraService:id,name,icon,description,image',
                'faqs:id,vehicle_id,question,answer',
                'damages:id,Vehicle_id,damage_type,damage_loaction,image,description',
                'tariffs:id,vehicle_id,tariff_title,tariff_daily_price,tariff_from_days,tariff_to_days,tariff_base_km,tariff_extra_price',
                'seasonals:id,vehicle_id,seasonal_title,seasonal_start_date,seasonal_end_date,seasonal_daily_rate,seasonal_weekly_rate,seasonal_monthly_rate,seasonal_late_fee',
                'owner.userDetails:id,user_id,profile_image'
            ]);

            $vehicles = $query->where('slug', $vehicleSlug)->get();

            if ($vehicles->isEmpty()) {
                return [
                    'code'    => 404,
                    'success' => false,
                    'message' => 'No vehicle found with the provided slug.',
                    'data'    => [],
                ];
            }

            $data = [];

            foreach ($vehicles as $vehicle) {
                $featureIds = json_decode($vehicle->features ?? '', true);
                $featureNames = SafetyFeature::whereIn('id', $featureIds)->pluck('feature');

                $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                    ->where('key', 'vehicle_image')
                    ->first();
                $vehiclepolicys = VehicleMeta::where('vehicle_id', $vehicle->id)
                    ->where('key', 'vehicle_policy')
                    ->first();
                $vehicleDoc = VehicleMeta::where('vehicle_id', $vehicle->id)
                    ->where('key', 'vehicle_doc')
                    ->first();
                $filteredPrices = [];
                $vehiclePrices = json_decode($vehicle->vehicle_price ?? '', true);
                if (!empty($vehiclePrices)) {
                    foreach ($vehiclePrices as $price) {
                        foreach ($price as $key => $value) {
                            if ($value > 0) {
                                $filteredPrices[] = [$key => $value]; // Store each key-value pair as a separate object
                            }
                        }
                    }
                }

                $multiplePolicy = $vehiclepolicys ? json_decode($vehiclepolicys->value, true) : [];
                $multipleDoc = $vehicleDoc ? json_decode($vehicleDoc->value, true) : [];

                $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

                if (!empty($vehicle->vehicle_image)) {
                    array_unshift($multipleImages, $vehicle->vehicle_image);
                }

                $multipleImages = array_map(function ($img) {
                    $img = '/' . ltrim($img, '/'); // Ensure single leading slash

                    $img = str_replace(self::VEHICLE_IMAGE, 'vehicles/images/medium/', $img);

                    return url('storage' . $img);
                }, $multipleImages);

                if (Auth::guard('web')->check()) {
                    $user = Auth::guard('web')->user();
                    $wishlist = Wishlist::where('user_id', Auth::id())->where('vehicle_id', $vehicle->id)->first();
                }
                $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;
                /** @var \App\Models\User|null $auth */
                $auth = current_user();
                $authId = $auth?->id;

                $wishlistExists = false;

                $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();

                if ($currencySetting && $currencySetting->value) {
                    $currency = Currency::find($currencySetting->value);
                }
                if ($authId) {
                    $wishlistExists = Wishlist::where("user_id", $authId)
                        ->where("vehicle_id", $vehicle->id)
                        ->exists();
                }
                $faqEnabled = GeneralSetting::where('group_id', 20)->where('key', 'faq')->first()->value;
                $extraServiceEnabled = GeneralSetting::where('group_id', 20)->where('key', 'extraService')->first()->value;
                $data = [
                    'id'                      => $vehicle->id,
                    'name'                    => $vehicle->name,
                    'slug'                    => $vehicle->slug,
                    'vehicle_image'           => url(self::STORAGES . $vehicle->vehicle_image),
                    'multiple_vehicle_doc'    => array_map(fn ($doc) => url(self::STORAGE . ($doc)), $multipleDoc),
                    'multiple_vehicle_policy' => array_map(fn ($policy) => url(self::STORAGE . ($policy)), $multiplePolicy),
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
                    'features'                => $featureNames,
                    'currency'                => getDefaultCurrencySymbol(),
                    'seo_title'               => $vehicle->vehicle_metatitle,
                    'seo_key'                 => $vehicle->vehicle_metakeywords,
                    'seo_description'         => $vehicle->vehicle_metadesc,
                    'is_featured'             => (bool) rand(0, 1),
                    'is_top_rated'            => (bool) rand(0, 1),
                    'authenticated'           => Auth::guard('web')->check(),
                    'description'             => $vehicle->description,
                    'extraservice'            => $extraServiceEnabled ? $vehicle->extraservices->map(function (VehicleExtraService $extraservice) {
                        return [
                            'extra_service_id' => $extraservice->extra_service_id,
                            'value'            => $extraservice->value,
                            'price'            => $extraservice->price,
                            'name'             => optional($extraservice->extraService)->name,
                            'icon'             => uploadedAsset(optional($extraservice->extraService)->icon), // Convert icon to full URL
                            'description'      => optional($extraservice->extraService)->description,
                            'image'            => url(self::STORAGES . optional($extraservice->extraService)->image), // Convert image to full URL
                        ];
                    }) : null,
                    'tariff' => $vehicle->tariffs->map(function (VehicleTarrif $tariff) {
                        return [
                            'tariff_title'       => $tariff->tariff_title,
                            'tariff_daily_price' => $tariff->tariff_daily_price,
                            'tariff_from_days'   => $tariff->tariff_from_days,
                            'tariff_to_days'     => $tariff->tariff_to_days,
                            'tariff_base_km'     => $tariff->tariff_base_km,
                            'tariff_extra_price' => $tariff->tariff_extra_price,
                        ];
                    }),
                    'seasonal' => $vehicle->seasonals->map(function (VehicleSeason $seasonal) {
                        return [
                            'seasonal_title'        => $seasonal->seasonal_title,
                            'seasonal_start_date'   => $seasonal->seasonal_start_date,
                            'seasonal_end_date'     => $seasonal->seasonal_end_date,
                            'seasonal_daily_rate'   => $seasonal->seasonal_daily_rate,
                            'seasonal_weekly_rate'  => $seasonal->seasonal_weekly_rate,
                            'seasonal_monthly_rate' => $seasonal->seasonal_monthly_rate,
                            'seasonal_late_fee'     => $seasonal->seasonal_late_fee,
                        ];
                    }),
                    'faqs' => $faqEnabled ? $vehicle->faqs->map(function (VehicleFaq $faq) {
                        return [
                            'question' => $faq->question,
                            'answer'   => $faq->answer,
                        ];
                    }) : [],
                    'damages' => $vehicle->damages->map(function (VehicleDamage $damage) {
                        return [
                            'damage_type'     => $damage->damage_type,
                            'damage_loaction' => $damage->damage_loaction,
                            'image'           => $damage->image,
                            'description'     => $damage->description,
                        ];
                    }),
                    'owner_details' => $vehicle->owner ? [
                        'name'         => $vehicle->owner->name,
                        'phone_number' => $vehicle->owner->mobile_number,
                        'email'        => $vehicle->owner->email,
                        'image'        => $vehicle->owner->userDetails ? url(self::STORAGES . $vehicle->owner->userDetails->profile_image) : null
                    ] : null
                ];
            }

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $data
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error')
            ];
        }
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

        try {
            $vehicleMeta = VehicleMeta::where('vehicle_id', $request->vehicle_id)
                ->where('key', 'vehicle_image')
                ->first();

            if (!$vehicleMeta) {
                return [
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Vehicle images not found.'
                ];
            }

            $images = json_decode($vehicleMeta->value, true);

            // Extract relative path from full URL if needed
            $relativePath = null;
            $imageToDelete = parse_url($request->image_path, PHP_URL_PATH);
            if (is_string($imageToDelete)) {
                $relativePath = ltrim(str_replace(self::STORAGES, '', $imageToDelete), '/');
            }

            // Find and remove image
            if (($key = array_search($relativePath, $images)) !== false) {
                unset($images[$key]);
                if ($relativePath) {
                    Storage::delete($relativePath);
                }
                $vehicleMeta->value = json_encode(array_values($images)) ?: '';
                $vehicleMeta->save();

                return [
                    'code'    => 200,
                    'success' => true,
                    'message' => 'Image deleted successfully.'
                ];
            }

            return [
                'code'    => 404,
                'success' => false,
                'message' => 'Image not found in database.'
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_delete_error')
            ];
        }
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
        $authUser = current_user();
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

        $vehicles = VehicleInfo::with([
            self::CAR_TYPE,
            self::BRAND,
            self::CATEGORY,
            self::MAIN_LOCATION,
            self::COLOR,
            'fuel_type:id,fuel_type',
            'transmission:id,name',
            'reviews:id,vehicle_id,average_ratings'
        ])->where("language_id", $lang_id)->where('category_id', $request->category_id)->take(6)->get();

        $data = $vehicles->map(function ($vehicle) {
            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                ->where('key', 'vehicle_image')
                ->first();

            $vehiclePrices = json_decode((string) $vehicle->vehicle_price, true) ?? [];
            $filteredPrices = [];

            if (!empty($vehiclePrices)) {
                foreach ($vehiclePrices as $price) {
                    foreach ($price as $key => $value) {
                        if ($value > 0) {
                            $filteredPrices[] = [$key => $value];
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

            /** @var \App\Models\User $auth|null */
            $auth = current_user();
            $authId = $auth->id ?? null;

            $wishlistExists = false;
            if ($authId) {
                $wishlistExists = Wishlist::where("user_id", $authId)
                    ->where("vehicle_id", $vehicle->id)
                    ->exists();
            }


            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
            $currency = null;

            if ($currencySetting && $currencySetting->value) {
                $currency = Currency::find($currencySetting->value);
            }

            $currencySymbol = $currency->symbol ?? "$";

            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;
            $review_count = Review::where("vehicle_id", $vehicle->id)->count();

            $defaultAvatar = asset('/backend/assets/img/default-profile.png');
            $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;
            $avatarImage = $defaultAvatar;

            if ($profileImagePath) {
                $avatarImage = uploadedAsset($profileImagePath, 'profile');
            }

            return [
                'id'                 => $vehicle->id,
                'name'               => $vehicle->name,
                'slug'               => $vehicle->slug,
                'vehicle_image'      => url(self::STORAGES . str_replace(self::VEHICLE_IMAGE, self::VEHICLE_IMAGE_SMALL, $vehicle->vehicle_image)),
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
                'is_featured'        => (bool) rand(0, 1),
                'is_top_rated'       => (bool) rand(0, 1),
                'seo_title'          => $vehicle->vehicle_metatitle,
                'seo_key'            => $vehicle->vehicle_metakeywords,
                'seo_description'    => $vehicle->vehicle_metadesc,
                'authenticated'      => Auth::guard('web')->check(),
                'created_at'         => $vehicle->created_at,
                'status'             => $vehicle->status,
            ];
        });

        $html = view('frontend.home.list.recommended-vehicles', compact('data'))->render();
        return [
            'code'    => 200,
            'message' => __('Vehicles retrieved successfully.'),
            'html'    => $html
        ];
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
