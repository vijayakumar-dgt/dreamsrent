<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\ImageResizer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleDamage;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Repositories\Contracts\VehicleInfoRepositoryInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Modules\CarInfo\Models\CarModel;
use Modules\CarInfo\Models\SafetyFeature;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleFaq;
use Modules\CarInfo\Models\VehicleInsurance;
use Modules\CarInfo\Models\VehicleMeta;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;

class VehicleInfoRepository implements VehicleInfoRepositoryInterface
{
    protected ImageResizer $imageResizer;

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

        $data = [
            'vechileName' => $vechileName,
            'vechileType' => $vechileType,
            'vechileLocation' => $vechileLocation,
        ];

        return $data;
    }

    public function checkVehicle(Request $request): array
    {
        try {
            $vehicleSlug = $request->get('vehicle_slug');

            // Find vehicle directly instead of checking twice
            $vehicle = VehicleInfo::where('slug', $vehicleSlug)->first();

            if (!$vehicle) {
                return [
                    'code' => 404,
                    'exists' => 'no'
                ];
            }

            return [
                'code' => 200,
                'exists' => 'yes'
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'error' => __('admin.common.default_retrieve_error')
            ];
        }
    }

    public function seasonalInfo(?int $vehicleId): array
    {
        try {
            $vehicleSeasons = VehicleSeason::where("vehicle_id", $vehicleId)->get();

            return [
                'code'  => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $vehicleSeasons
            ];
        } catch (\Exception $e) {
            return [
                'code'  => 500,
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
                'code'  => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $vehicleTrraifs
            ];
        } catch (\Exception $e) {
            return [
                'code'  => 500,
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
                'vehicle_images' => [],
                'vehicle_docs' => [],
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
                            return asset('storage/' . $image); // Convert to URL format
                        }, $values);

                        $response['vehicle_images'] = array_merge($response['vehicle_images'], $formattedImages);
                        break;

                    case 'vehicle_doc':
                        $response['vehicle_docs'] = array_merge($response['vehicle_docs'], $values);
                        break;

                    case 'vehicle_policy':
                        $response['vehicle_policies'] = array_merge($response['vehicle_policies'], $values);
                        break;
                }
            }

            return [
                'code'  => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $response
            ];
        } catch (\Exception $e) {
            return [
                'code'  => 500,
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
                'code'  => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $vehicleFaqs
            ];
        } catch (\Exception $e) {
            return [
                'code'  => 500,
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
                'code'  => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $vehicleDamages
            ];
        } catch (\Exception $e) {
            return [
                'code'  => 500,
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
                    'id' => $insurance->id,
                    'vehicle_id' => $insurance->vehicle_id,
                    'insurances_id' => $insurance->insurances_id,
                    'insurance_name' => optional($insurance->insurance)->insurance_name, // Get insurance name
                    'value' => $insurance->value,
                    'price' => $insurance->price,
                    'benefits' => $formattedBenefits, // Count formatted
                    'created_at' => $insurance->created_at,
                    'updated_at' => $insurance->updated_at,
                    'deleted_at' => $insurance->deleted_at,
                ];
            });

            return [
                'code'  => 200,
                'success' => true,
                'data' => $insuranceData
            ];
        } catch (\Exception $e) {
            return [
                'code'  => 500,
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
                'code'  => 200,
                'success' => true,
                'data' => $models
            ];
        } catch (\Exception $e) {
            return [
                'code'  => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function vehicleDetailsList(Request $request): array
    {
        try {
            $vehicleSlug = $request->vehicle_slug;

            if (!$vehicleSlug) {
                return [
                    'code'  => 400,
                    'success' => false,
                    'message' => 'Vehicle ID is required'
                ];
            }

            $query = VehicleInfo::with([
                'carType:id,name',
                'brand:id,brand_name',
                'category:id,name',
                'mainLocation:id,name',
                'color:id,name,value',
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
                    'code'  => 404,
                    'success' => false,
                    'message' => 'No vehicle found with the provided slug.',
                    'data' => [],
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

                $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];
                $multiplePolicy = $vehiclepolicys ? json_decode($vehiclepolicys->value, true) : [];
                $multipleDoc = $vehicleDoc ? json_decode($vehicleDoc->value, true) : [];

                $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

                if (!empty($vehicle->vehicle_image)) {
                    array_unshift($multipleImages, $vehicle->vehicle_image);
                }

                $multipleImages = array_map(function ($img) {
                    $img = '/' . ltrim($img, '/'); // Ensure single leading slash
                    return url('storage' . $img);
                }, $multipleImages);
                $user = null;
                $wishlist = null;
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
                $currency = null;

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
                    'id' => $vehicle->id,
                    'name' => $vehicle->name,
                    'slug' => $vehicle->slug,
                    'vehicle_image' => url('/storage/' . $vehicle->vehicle_image),
                    'multiple_vehicle_doc' => array_map(fn($doc) => url('storage/' . ($doc)), $multipleDoc),
                    'multiple_vehicle_policy' => array_map(fn($policy) => url('storage/' . ($policy)), $multiplePolicy),
                    'multiple_vehicle_images' => $multipleImages,
                    'has_multiple_image' => count($multipleImages) > 1,
                    'brand' => $vehicle->brand->brand_name ?? null,
                    'car_type' => $vehicle->carType->name ?? null,
                    'category' => $vehicle->category->name ?? null,
                    'location' => $vehicle->mainLocation->name ?? null,
                    'color' => $vehicle->color->name ?? null,
                    'fuel_type' => $vehicle->fuel_type->fuel_type ?? null,
                    'transmission' => $vehicle->transmission->name ?? null,
                    'wishlist' => $wishlistExists,
                    'year' => $vehicle->year,
                    'mileage' => $vehicle->mileage,
                    'vin' => $vehicle->vin,
                    'rating' => $rating,
                    'passenger_capacity' => $vehicle->passenger_capacity,
                    'num_seats' => $vehicle->num_seats,
                    'num_doors' => $vehicle->num_doors,
                    'num_airbags' => $vehicle->num_airbags,
                    'vehicle_video' => $vehicle->vehicle_video,
                    'price' => !empty($filteredPrices) ? $filteredPrices : null,
                    'created_at' => $vehicle->created_at,
                    'features' => $featureNames,
                    'currency' => getDefaultCurrencySymbol(),
                    'seo_title' => $vehicle->vehicle_metatitle,
                    'seo_key' => $vehicle->vehicle_metakeywords,
                    'seo_description' => $vehicle->vehicle_metadesc,
                    'is_featured' => (bool) rand(0, 1),
                    'is_top_rated' => (bool) rand(0, 1),
                    'authenticated' => Auth::guard('web')->check(),
                    'description' => $vehicle->description,
                    'extraservice' => $extraServiceEnabled ? $vehicle->extraservices->map(function (VehicleExtraService $extraservice) {
                        return [
                            'extra_service_id' => $extraservice->extra_service_id,
                            'value' => $extraservice->value,
                            'price' => $extraservice->price,
                            'name' => optional($extraservice->extraService)->name,
                            'icon' => uploadedAsset(optional($extraservice->extraService)->icon), // Convert icon to full URL
                            'description' => optional($extraservice->extraService)->description,
                            'image' => url('/storage/' . optional($extraservice->extraService)->image), // Convert image to full URL
                        ];
                    }) : null,
                    'tariff' => $vehicle->tariffs->map(function (VehicleTarrif $tariff) {
                        return [
                            'tariff_title' => $tariff->tariff_title,
                            'tariff_daily_price' => $tariff->tariff_daily_price,
                            'tariff_from_days' => $tariff->tariff_from_days,
                            'tariff_to_days' => $tariff->tariff_to_days,
                            'tariff_base_km' => $tariff->tariff_base_km,
                            'tariff_extra_price' => $tariff->tariff_extra_price,
                        ];
                    }),
                    'seasonal' => $vehicle->seasonals->map(function (VehicleSeason $seasonal) {
                        return [
                            'seasonal_title' => $seasonal->seasonal_title,
                            'seasonal_start_date' => $seasonal->seasonal_start_date,
                            'seasonal_end_date' => $seasonal->seasonal_end_date,
                            'seasonal_daily_rate' => $seasonal->seasonal_daily_rate,
                            'seasonal_weekly_rate' => $seasonal->seasonal_weekly_rate,
                            'seasonal_monthly_rate' => $seasonal->seasonal_monthly_rate,
                            'seasonal_late_fee' => $seasonal->seasonal_late_fee,
                        ];
                    }),
                    'faqs' => $faqEnabled ? $vehicle->faqs->map(function (VehicleFaq $faq) {
                        return [
                            'question' => $faq->question,
                            'answer' => $faq->answer,
                        ];
                    }) : [],
                    'damages' => $vehicle->damages->map(function (VehicleDamage $damage) {
                        return [
                            'damage_type' => $damage->damage_type,
                            'damage_loaction' => $damage->damage_loaction,
                            'image' => $damage->image,
                            'description' => $damage->description,
                        ];
                    }),
                    'owner_details' => $vehicle->owner ? [
                        'name' => $vehicle->owner->name,
                        'phone_number' => $vehicle->owner->mobile_number,
                        'email' => $vehicle->owner->email,
                        'image' => $vehicle->owner->userDetails ? url('/storage/' . $vehicle->owner->userDetails->profile_image) : null
                    ] : null
                ];
            }

            return [
                'code' => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error')
            ];
        }
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
                    'code' => 404,
                    'success' => false,
                    'message' => 'Vehicle images not found.'
                ];
            }

            $images = json_decode($vehicleMeta->value, true);

            // Extract relative path from full URL if needed
            $relativePath = null;
            $imageToDelete = parse_url($request->image_path, PHP_URL_PATH);
            if (is_string($imageToDelete)) {
                $relativePath = ltrim(str_replace('/storage/', '', $imageToDelete), '/');
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
                    'code' => 200,
                    'success' => true,
                    'message' => 'Image deleted successfully.'
                ];
            }

            return [
                'code' => 404,
                'success' => false,
                'message' => 'Image not found in database.'
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
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

        try {
            // Ensure the correct path format
            $filePath = 'vehicles/policy/' . $request->file_path;

            $vehicleMeta = VehicleMeta::where('vehicle_id', $request->vehicle_id)
                ->where('key', 'vehicle_policy')
                ->first();

            if (!$vehicleMeta) {
                return [
                    'code' => 404,
                    'success' => false,
                    'message' => 'Policy files not found.'
                ];
            }

            $policyFiles = json_decode($vehicleMeta->value, true);

            // Find and remove the file from the array
            if (($key = array_search($filePath, $policyFiles)) !== false) {
                unset($policyFiles[$key]);
                Storage::delete($filePath); // Delete from storage
                $vehicleMeta->value = json_encode(array_values($policyFiles)) ?: '';
                $vehicleMeta->save();

                return [
                    'code' => 200,
                    'success' => true,
                    'message' => 'Policy file deleted successfully.'
                ];
            }

            return [
                'code' => 404,
                'success' => false,
                'message' => 'Policy file not found.'
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_delete_error')
            ];
        }
    }

    public function vehicleInterestLists(): array
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
            'carType:id,name',
            'brand:id,brand_name',
            'category:id,name',
            'mainLocation:id,name',
            'color:id,name,value',
            'fuel_type:id,fuel_type',
            'transmission:id,name',
            'reviews:id,vehicle_id,average_ratings'
        ])->where("language_id", $lang_id)->take(6)->get();

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
            $multipleImages = array_map(fn($img) => url('storage/vehicles/' . basename($img)), $multipleImages);
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
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'slug' => $vehicle->slug,
                'vehicle_image' => uploadedAsset($vehicle->vehicle_image ?? '', 'default2'),
                'avatar_image' => $avatarImage,
                'brand' => $vehicle->brand->brand_name ?? null,
                'car_type' => $vehicle->carType->name ?? null,
                'category' => $vehicle->category->name ?? null,
                'location' => $vehicle->mainLocation->name ?? null,
                'color' => $vehicle->color->name ?? null,
                'fuel_type' => $vehicle->fuel_type->fuel_type ?? null,
                'transmission' => $vehicle->transmission->name ?? null,
                'year' => $vehicle->year,
                'mileage' => $vehicle->mileage,
                'passenger_capacity' => $vehicle->passenger_capacity,
                'num_seats' => $vehicle->num_seats,
                'num_doors' => $vehicle->num_doors,
                'num_airbags' => $vehicle->num_airbags,
                'vehicle_video' => $vehicle->vehicle_video,
                'features' => $vehicle->features,
                'currency' => $currencySymbol,
                'rating' => $rating,
                'wishlist' => $wishlistExists,
                'review_count' => $review_count,
                'price' => !empty($filteredPrices) ? $filteredPrices : null,
                'is_featured' => (bool) rand(0, 1),
                'is_top_rated' => (bool) rand(0, 1),
                'seo_title' => $vehicle->vehicle_metatitle,
                'seo_key' => $vehicle->vehicle_metakeywords,
                'seo_description' => $vehicle->vehicle_metadesc,
                'authenticated' => Auth::guard('web')->check(),
                'created_at' => $vehicle->created_at,
                'status' => $vehicle->status,
            ];
        });

        $html = view('frontend.home.list.recommended-vehicles', compact('data'))->render();
        return [
            'code' => 200,
            'message' => __('Vehicles retrieved successfully.'),
            'html' => $html
        ];
    }

    public function getDamageDetails(?int $id)
    {
        $damage = VehicleDamage::find($id);

        if ($damage) {
            return [
                'code' => 200,
                'success' => true,
                'data' => $damage
            ];
        }

        return response()->json([
            'code' => 404,
            'success' => false,
            'message' => 'Damage not found.'
        ]);
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
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.vehicle_delete_success'),
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'code'   => 500,
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
                    'status' => 'error',
                    'code'   => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->popular = $request->popular ? 1 : 0;
            $vehicle->save();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.popular_status_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'code'   => 500,
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
                    'status' => 'error',
                    'code'   => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->recommended = $request->recommended ? 1 : 0;
            $vehicle->save();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.recommended_status_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'code'   => 500,
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
                    'status' => 'error',
                    'code'   => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->status = $request->status ? 1 : 0;
            $vehicle->save();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.common.default_status_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_status_error'),
            ];
        }
    }
}