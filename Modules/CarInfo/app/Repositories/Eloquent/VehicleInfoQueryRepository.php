<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\CarModel;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleDamage;
use Modules\CarInfo\Models\VehicleFaq;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleInsurance;
use Modules\CarInfo\Models\VehicleMeta;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;
use Modules\CarInfo\Repositories\Contracts\VehicleQueryRepositoryInterface;
use Modules\CarInfo\Repositories\Support\VehicleDataFormatter;
use Modules\CarInfo\Repositories\Support\VehicleQueryFilter;
use Modules\CarInfo\Repositories\Support\VehicleRepositoryBase;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;

class VehicleInfoQueryRepository extends VehicleRepositoryBase implements VehicleQueryRepositoryInterface
{
    public function __construct(
        private readonly VehicleDataFormatter $formatter,
        private readonly VehicleQueryFilter $filter
    ) {
    }

    public function index(): array
    {
        $languageId = currentUser()->language_id ?? 1;

        return [
            'vechileName'     => VehicleInfo::orderBy('id', 'desc')->where('language_id', $languageId)->get(),
            'vechileType'     => Cartype::orderBy('id', 'desc')->where('language_id', $languageId)->get(),
            'vechileLocation' => Location::orderBy('id', 'desc')->where('language_id', $languageId)->get(),
        ];
    }

    public function getVehicleList(): array
    {
        $vehicles = VehicleInfo::orderBy('id', 'desc')->get();

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $vehicles,
        ];
    }

    public function getDamageDetails(Request $request): array
    {
        $damage = VehicleDamage::find($request->id);

        return [
            'code'    => 200,
            'success' => true,
            'data'    => $damage,
        ];
    }

    public function vehicleInterestLists(Request $request): array
    {
        $languageId = $this->resolveFrontendLanguageId();
        $vehicles = VehicleInfo::with([
            self::CAR_TYPE,
            self::BRAND,
            self::CATEGORY,
            self::MAIN_LOCATION,
            self::COLOR,
            self::FUEL_TYPE,
            self::TRANSMISSION,
            'reviews:id,vehicle_id,average_ratings',
        ])->where('language_id', $languageId)
            ->where('category_id', $request->category_id)
            ->take(6)
            ->get();
        $data = $vehicles->map(fn($vehicle) => $this->formatter->formatVehicleInterest($vehicle));
        $html = view('frontend.home.list.recommended-vehicles', compact('data'))->render();

        return [
            'code'    => 200,
            'message' => __('Vehicles retrieved successfully.'),
            'html'    => $html,
        ];
    }

    public function vehicleDetailsList(Request $request): array
    {
        try {
            $vehicleSlug = $request->vehicle_slug;
            if (!$vehicleSlug) {
                return $this->errorResponse('Vehicle ID is required', 400);
            }

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
                'owner.userDetails:id,user_id,profile_image',
            ])->where('slug', $vehicleSlug)->first();

            if (!$vehicle) {
                return $this->errorResponse(__('admin.common.no_data_found'), 404);
            }

            $extraServiceEnabled = $this->isSettingEnabled(6, 'extra_service_enable');
            $faqEnabled = $this->isSettingEnabled(6, 'faq_enable');

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $this->formatter->formatVehicleDetails($vehicle, $extraServiceEnabled, $faqEnabled),
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }
    }

    public function getModel(?int $brandId): array
    {
        try {
            $models = CarModel::where('brand_id', $brandId)->get(['id', 'model_name']);

            return [
                'code'    => 200,
                'success' => true,
                'data'    => $models,
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
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
        } catch (\Throwable $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function insurance(?int $vehicleId): array
    {
        try {
            $vehicleInsurance = VehicleInsurance::where('vehicle_id', $vehicleId)
                ->with(['insurance', 'insuranceBenefits'])
                ->get();

            $insuranceData = $vehicleInsurance->map(function (VehicleInsurance $insurance) {
                $benefitCount = $insurance->insuranceBenefits->count();
                $formattedBenefits = str_pad((string) $benefitCount, 2, '0', STR_PAD_LEFT);

                return [
                    'id'             => $insurance->id,
                    'vehicle_id'     => $insurance->vehicle_id,
                    'insurances_id'  => $insurance->insurances_id,
                    'insurance_name' => optional($insurance->insurance)->insurance_name,
                    'value'          => $insurance->value,
                    'price'          => $insurance->price,
                    'benefits'       => $formattedBenefits,
                    'created_at'     => $insurance->created_at,
                    'updated_at'     => $insurance->updated_at,
                    'deleted_at'     => $insurance->deleted_at,
                ];
            });

            return [
                'code'    => 200,
                'success' => true,
                'data'    => $insuranceData,
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }
    }

    public function damage(?int $vehicleId): array
    {
        try {
            $vehicleDamages = VehicleDamage::where('vehicle_id', $vehicleId)->get();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $vehicleDamages,
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }
    }

    public function faq(?int $vehicleId): array
    {
        try {
            $vehicleFaqs = VehicleFaq::where('vehicle_id', $vehicleId)->get();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $vehicleFaqs,
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }
    }

    public function documents(?int $vehicleId): array
    {
        try {
            $documents = VehicleMeta::where('vehicle_id', $vehicleId)->get();

            $response = [
                'vehicle_images'   => [],
                'vehicle_docs'     => [],
                'vehicle_policies' => [],
            ];

            foreach ($documents as $document) {
                $values = json_decode($document->value, true);
                if (!is_array($values)) {
                    continue;
                }

                switch ($document->key) {
                    case 'vehicle_image':
                        $formatted = array_map(fn($image) => asset(self::STORAGE . $image), $values);
                        $response['vehicle_images'] = array_merge($response['vehicle_images'], $formatted);
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
                'data'    => $response,
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }
    }

    public function seasonalInfo(?int $vehicleId): array
    {
        try {
            $vehicleSeasons = VehicleSeason::where('vehicle_id', $vehicleId)->get();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $vehicleSeasons,
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }
    }

    public function tariffInfo(?int $vehicleId): array
    {
        try {
            $vehicleTariffs = VehicleTarrif::where('vehicle_id', $vehicleId)->get();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $vehicleTariffs,
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }
    }

    public function checkVehicle(Request $request): array
    {
        try {
            $vehicleSlug = $request->get('vehicle_slug');
            $vehicle = VehicleInfo::where('slug', $vehicleSlug)->first();

            if (!$vehicle) {
                return [
                    'code'   => 404,
                    'exists' => 'no',
                ];
            }

            return [
                'code'   => 200,
                'exists' => 'yes',
            ];
        } catch (\Throwable $e) {
            return $this->errorResponse(__('admin.common.default_retrieve_error'), 500);
        }
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
            'reviews:id,vehicle_id,average_ratings',
        ]);

        $languageId = $this->resolveFrontendLanguageId();

        $locationResult = $this->applyCustomerFilters($query, $request);
        if ($locationResult !== null) {
            return $locationResult;
        }

        $this->filter->applyCustomerSorting($query, $request);

        if (!is_null($request->sort_by_date)) {
            $this->filter->applyDateFilter($query, $request->sort_by_date);
        }

        $perPage = $request->paginate ?? 1;
        $vehicles = $query->where('language_id', $languageId)->where('status', 1)->paginate($perPage);
        $data = $vehicles->map(fn(VehicleInfo $vehicle) => $this->formatter->formatVehicleListItem($vehicle));

        return [
            'code'       => 200,
            'message'    => __('web.common.default_retrieve_success'),
            'data'       => $data,
            'pagination' => [
                'total'         => $vehicles->total(),
                'per_page'      => $vehicles->perPage(),
                'current_page'  => $vehicles->currentPage(),
                'last_page'     => $vehicles->lastPage(),
                'from'          => $vehicles->firstItem(),
                'to'            => $vehicles->lastItem(),
                'next_page_url' => $vehicles->nextPageUrl(),
                'prev_page_url' => $vehicles->previousPageUrl(),
            ],
        ];
    }

    public function adminVehicleList(Request $request): array
    {
        $response = [
            'code'    => 500,
            'status'  => 'error',
            'message' => __('admin.common.default_retrieve_error'),
        ];

        try {
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
                self::COLOR,
            ])->select([
                'id', 'vehicle_image', 'name', 'slug', 'type_id', 'brand_id', 'category_id', 'main_location_id',
                'color_id', 'vehicle_price', 'vehicle_basekm', 'created_at', 'perma_link', 'model_id', 'plate_number',
                'vin', 'other_location_id', 'fuel_type_id', 'odometer', 'year', 'transmission_id', 'mileage',
                'passenger_capacity', 'num_seats', 'num_doors', 'num_airbags', 'vehicle_video', 'vehicle_extrakmprice',
                'vehicle_metatitle', 'vehicle_metadesc', 'vehicle_metakeywords', 'features', 'popular', 'recommended',
                'status',
            ])->where('language_id', $languageId);

            $this->filter->applyAdminFilters($query, $request);
            $this->filter->applyAdminSorting($query, $request);

            $vehicles = $query->get()->map(fn($vehicle) => $this->formatter->transformForAdminList($vehicle));

            return [
                'code'    => 200,
                'status'  => 'success',
                'message' => __('web.common.default_retrieve_success'),
                'data'    => $vehicles,
            ];
        } catch (\Throwable $e) {
            return $response;
        }
    }

    private function resolveFrontendLanguageId(): int
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

        $defaultLang = Language::select('language_id')->where('default', 1)->first();
        return $defaultLang->language_id ?? 1;
    }

    private function applyCustomerFilters($query, Request $request): ?array
    {
        $pickupDatetime = $request->pickup_datetime;
        $returnDatetime = $request->return_datetime;
        $unavailableVehicleIds = [];

        if (!empty($request->location)) {
            $location = Location::where('name', 'LIKE', "%{$request->location}%")->first();

            if (!$location) {
                return [
                    'code'    => 200,
                    'message' => __('web.common.default_retrieve_success'),
                    'data'    => [],
                ];
            }

            if (!empty($pickupDatetime) && !empty($returnDatetime)) {
                $unavailableVehicleIds = Booking::where(function ($bookingQuery) use ($pickupDatetime, $returnDatetime) {
                    $bookingQuery->where('start_datetime', '<', $returnDatetime)
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
                            (float) $rate + 0.99,
                        ]);
                    }
                });
            });
        }

        $fromPrice = $request->from_price ?? 1;
        $toPrice = $request->to_price ?? null;

        if (!empty($toPrice)) {
            $fromPrice = ($fromPrice <= 0) ? 1 : $fromPrice;

            $query->whereRaw(
                "CAST(JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) AS UNSIGNED) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) > 0",
                [$fromPrice, $toPrice]
            );
        }

        if (!empty($request->rent_type)) {
            $rentType = $request->rent_type;

            $query->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].$rentType')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].$rentType')) > 0"
            );
        }

        if (!empty($request->vehicle_location_id) && is_array($request->vehicle_location_id)) {
            $query->whereIn('main_location_id', $request->vehicle_location_id);
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        return null;
    }

}
