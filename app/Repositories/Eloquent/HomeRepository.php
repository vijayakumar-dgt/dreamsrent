<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserDetail;
use App\Repositories\Contracts\HomeRepositoryInterface;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\CarColor;
use Modules\CarInfo\Models\CarFuel;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\SafetyFeature;
use Modules\CarInfo\Models\Transmission;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\GeneralSetting;

class HomeRepository implements HomeRepositoryInterface
{
    public function getHomeData(): string
    {
        $defaultTheme = GeneralSetting::where('key', 'default_theme')->first();
        $theme = $defaultTheme->value ?? 1;
        $viewPath = 'frontend.home.home_' . $theme;
        if (!view()->exists($viewPath)) {
            $viewPath = 'frontend.home.home_1';
        }
        return $viewPath;
    }

    public function getVehicles(): array
    {
        $languageCode = app()->getLocale();
        $languageId = getLanguageId($languageCode);
        $brands = Brand::where('status', 1)->where("language_id", $languageId)->orderBy('brand_name', 'asc')->get();
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\CarInfo\Models\Cartype> $cartypes */
        $cartypes = Cartype::where('language_id', $languageId)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        /** @var \Illuminate\Support\Collection<int, array{id: int, name: string, vehicle_count: int}> $vehicleTypes */
        $vehicleTypes = $cartypes->map(function (Cartype $vehicleType) {
            $vehicleCount = VehicleInfo::where('type_id', $vehicleType->id)->count();
            return [
                'id' => $vehicleType->id,
                'name' => $vehicleType->name,
                'vehicle_count' => $vehicleCount
            ];
        });

        $years = VehicleInfo::where('language_id', $languageId)
                 ->select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        $fuelTypes = CarFuel::where('language_id', $languageId)
                    ->where('status', 1)->orderBy('fuel_type', 'asc')->get();
        $transmissions = Transmission::where('language_id', $languageId)
                    ->where('status', 1)->orderBy('name', 'asc')->get();
        $colors = CarColor::where('language_id', $languageId)
                   ->where('status', 1)->orderBy('name', 'asc')->get();
        $features = SafetyFeature::where('language_id', $languageId)
                    ->where('status', 1)->orderBy('feature', 'asc')->get();
        $allowBooking = GeneralSetting::where('group_id', 20)
                        ->where('key', 'booking')->value('value') ?? 1;
        $allowEnquiries = GeneralSetting::where('group_id', 20)->where('key', 'enquiries')->value('value') ?? 1;
        $pickuplocation = $request->pickuplocation ?? '';
        $pickupdate = "";
        $pickuptime = "";
        $returndate = "";
        $returntime = "";
        $defaultTheme = GeneralSetting::where('key', 'default_theme')->first();
        $theme = $defaultTheme->value ?? 1;
        if ($theme == 1) {
            $pickupdate = $request->pickupdate ?? '';
            $pickuptime = $request->pickuptime ?? '';
            $returndate = $request->returndate ?? '';
            $returntime = $request->returntime ?? '';
        } else {
            $pickupdatetime = $request->pickupdatetime ?? '';
            $returndatetime = $request->returndatetime ?? '';
            $pickupdate = $pickupdatetime ? date('d-m-Y', strtotime($pickupdatetime)) : '';
            $pickuptime = $pickupdatetime ? date('H:i:s', strtotime($pickupdatetime)) : '';
            $returndate = $returndatetime ? date('d-m-Y', strtotime($returndatetime)) : '';
            $returntime = $returndatetime ? date('H:i:s', strtotime($returndatetime)) : '';
        }
        $_pickuplocation = Location::select('id', 'name')->where('status', 1)->where('language_id', $languageId)->where('name', 'like', '%' . $pickuplocation . '%')->first();
        $data = [
            'brands' => $brands,
            'vehicleTypes' => $vehicleTypes,
            'years' => $years,
            'fuelTypes' => $fuelTypes,
            'transmissions' => $transmissions,
            'colors' => $colors,
            'features' => $features,
            'allowBooking' => $allowBooking,
            'allowEnquiries' => $allowEnquiries,
            'pickuplocation' => $pickuplocation,
            'pickupdate' => $pickupdate,
            'pickuptime' => $pickuptime,
            'returndate' => $returndate,
            'returntime' => $returntime,
            'theme' => $theme,
            'seo_title' => __('web.common.vehicles'),
            'initialPickupLocation' => $pickuplocation ? $_pickuplocation : null
        ];
        
        return $data;
    }

    public function getVehicleDetails(string $slug): array
    {
        $slug = $slug;

        $vehicle = VehicleInfo::select('id', 'main_location_id', "other_location_id", 'views')
            ->where('slug', $slug)->first();
        if (!$vehicle) {
            abort(404);
        }
        $mainLocation = Location::select('id', 'name', 'address')->where('id', $vehicle->main_location_id ?? '')->first();
        $allLocation = collect();

        // Get main location
        if ($vehicle->main_location_id) {
            $mainLocations = Location::select('id', 'name', 'address')
                ->where('id', $vehicle->main_location_id)
                ->first();

            if ($mainLocations) {
                $allLocation->push($mainLocations);
            }
        }

        // Get other locations and remove duplicates
        if (!empty($vehicle->other_location_id)) {
            $otherIds = json_decode($vehicle->other_location_id, true);

            if (is_array($otherIds)) {
                // Remove main_location_id if present in other_location_id
                $filteredOtherIds = array_filter($otherIds, function ($id) use ($vehicle) {
                    return $id != $vehicle->main_location_id;
                });

                if (!empty($filteredOtherIds)) {
                    $otherLocations = Location::select('id', 'name', 'address')
                        ->whereIn('id', $filteredOtherIds)
                        ->get();

                    $allLocation = $allLocation->merge($otherLocations);
                }
            }
        }
        if (isset($vehicle->views) && is_numeric($vehicle->views)) {
            $vehicle->increment('views');
        } else {
            $vehicle->update(['views' => 1]);
        }

        $bookingCount = Booking::where('vehicle_id', $vehicle->id)->count();

        $vehicleCount = VehicleInfo::select("views")->where('id', $vehicle->id)->first();

        $lastUpdate = VehicleInfo::where('id', $vehicle->id)->value('updated_at');

        $lastUpdateFormatted = formatDateTime($lastUpdate);

        $allowBooking = GeneralSetting::where('group_id', 20)
            ->where('key', 'booking')->value('value') ?? 1;
        $allowEnquiries = GeneralSetting::where('group_id', 20)
            ->where('key', 'enquiries')->value('value') ?? 1;
        $vehicleDetail = VehicleInfo::find($vehicle->id);
        $seo_title = '';
        $seo_description = '';
        $meta_keywords = '';
        $og_image = '';
        $mainLocation = null;

        $vehicleDetail = VehicleInfo::find($vehicle->id);

        if ($vehicleDetail) {
            $vehicleDetail->name = ucfirst($vehicleDetail->name ?? '');
            $vehicleDetail->location_name = $vehicleDetail->mainLocation->name ?? '';
            $vehicleDetail->image_url = $vehicleDetail->vehicle_image ? uploadedAsset($vehicleDetail->vehicle_image) : '';

            $seo_title = $vehicleDetail->vehicle_metatitle ?? '';
            $seo_description = $vehicleDetail->vehicle_metadesc ?? '';
            $meta_keywords = $vehicleDetail->vehicle_metakeywords ?? '';
            $og_image = $vehicleDetail->vehicle_image ? uploadedAsset($vehicleDetail->vehicle_image) : '';
            $mainLocation = $vehicleDetail->mainLocation;
        }

        $author_location = GeneralSetting::where('key', 'company_address_line')->first()->value ?? '';
        $appAdmin = User::where('user_type', 1)->first();
        $appAdminDetails = $appAdmin ? UserDetail::where('user_id', $appAdmin->id)->first() : null;
        $author_profile = $appAdminDetails ? uploadedAsset($appAdminDetails->profile_image, 'profile') : '';
        $author_email = $appAdmin->email ?? "";
        $author_phone = $appAdminDetails->mobile_number ?? "";
        $author_name = getCurrentUserFullname($appAdmin->id);
        $response = [
            'author_location' => $author_location,
            'author_profile' => $author_profile,
            'author_email' => $author_email,
            'author_phone' => $author_phone,
            'author_name' => $author_name,
            'vehicle' => $vehicle,
            'mainLocation' => $mainLocation,
            'allLocation' => $allLocation,
            'bookingCount' => $bookingCount,
            'vehicleCount' => $vehicleCount,
            'lastUpdate' => $lastUpdateFormatted,
            'allowBooking' => $allowBooking,
            'allowEnquiries' => $allowEnquiries,
            'vehicleDetail' => $vehicleDetail,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'meta_keywords' => $meta_keywords,
            'og_image' => $og_image,
            'slug' => $slug
        ];

        return $response;
    }

    public function searchLocations(string $keyword): array
    {
        if (!empty($keyword)) {
            $locations = Location::where("name", "LIKE", "%{$keyword}%")->select('id', 'name')->get();
        } else {
            $locations = collect();
        }
        $response = [
            'status' => true,
            'data'   => $locations
        ];

        return $response;
    }

    public function getMaintenanceData(): array
    {
        $companyName = GeneralSetting::where('key', 'organization_name')->first();
        $response['title'] = $companyName ? $companyName->value : 'Dreams Rent';
        $maintenance = GeneralSetting::where('group_id', 4)->pluck('value', 'key')->toArray();
        $response['image'] = $maintenance['maintenance_image'] ?
            uploadedAsset($maintenance['maintenance_image']) : '';
        $response['description'] = $maintenance['maintenance_description'] ?? "";

        return $response;
    }

    public function getContactData(): array
    {
        $companyPhoneNumber = GeneralSetting::where('key', 'company_phone')->first();
        $companyEmail = GeneralSetting::where('key', 'company_email')->first();
        $companyAddress = GeneralSetting::where('key', 'company_address_line')->first();
        $response['seo_title'] = __('web.user.contact_us');
        $response['companyPhoneNumber'] = $companyPhoneNumber ? $companyPhoneNumber->value : '';
        $response['companyEmail'] = $companyEmail ? $companyEmail->value : '';
        $response['companyAddress'] = $companyAddress ? $companyAddress->value : '';

        return $response;
    }
}