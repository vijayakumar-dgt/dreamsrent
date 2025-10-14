<?php

namespace Modules\CarInfo\Repositories\Support;

use Illuminate\Support\Facades\Auth;
use Modules\CarInfo\Models\VehicleDamage;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleFaq;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleMeta;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use App\Models\Review;
use App\Models\Wishlist;

class VehicleDataFormatter extends VehicleRepositoryBase
{
    public function transformForAdminList(VehicleInfo $vehicle): VehicleInfo
    {
        $vehicleImagePath = $vehicle->vehicle_image ?? '';
        $filename = basename($vehicleImagePath);
        $newPath = self::VEHICLE_IMAGE_SMALL . $filename;

        if (file_exists(public_path(self::STORAGE . $newPath))) {
            $vehicleImagePath = $newPath;
        }

        $vehicle->vehicle_image = uploadedAsset($vehicleImagePath);
        $vehicle->currency = getDefaultCurrencySymbol();
        $vehicle->multiple_vehicle_images = [];

        if ($vehicleMeta = VehicleMeta::where('vehicle_id', $vehicle->id)->where('key', 'vehicle_image')->first()) {
            $images = $vehicleMeta->value ? json_decode($vehicleMeta->value) : [];
            $vehicle->multiple_vehicle_images = array_map(fn($img) => url('storage/vehicles/' . basename($img)), $images);
        }

        $vehicle->has_multiple_image = count($vehicle->multiple_vehicle_images) > 1;
        $vehicle->damage_count = VehicleDamage::where('vehicle_id', $vehicle->id)->count();
        $vehicle->created_date = formatDateTime($vehicle->created_at, false);

        return $vehicle;
    }

    public function formatVehicleListItem(VehicleInfo $vehicle): array
    {
        $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)->where('key', 'vehicle_image')->first();
        $vehiclePrices = is_string($vehicle->vehicle_price) ? json_decode($vehicle->vehicle_price, true) : [];
        $filteredPrices = [];

        foreach ($vehiclePrices ?? [] as $price) {
            foreach ($price as $key => $value) {
                if ($value > 0) {
                    $filteredPrices[] = [$key => $value];
                }
            }
        }

        $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];
        if (!empty($vehicle->vehicle_image)) {
            array_unshift($multipleImages, $vehicle->vehicle_image);
        }

        $multipleImages = array_map(function ($img) {
            $img = '/' . ltrim($img, '/');
            $img = str_replace(self::VEHICLE_IMAGE, self::VEHICLE_IMAGE_SMALL, $img);
            return url('storage' . $img);
        }, $multipleImages);

        $auth = currentUser();
        $authId = $auth->id ?? null;
        $wishlistExists = false;

        if ($authId) {
            $wishlistExists = Wishlist::where('user_id', $authId)
                ->where('vehicle_id', $vehicle->id)
                ->exists();
        }

        $currencySymbol = getDefaultCurrencySymbol();
        $rating = Review::where('vehicle_id', $vehicle->id)->value('average_ratings') ?? 0;
        $reviewCount = Review::where('vehicle_id', $vehicle->id)->count();
        $defaultAvatar = asset('/backend/assets/img/default-profile.png');
        $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;
        $avatarImage = $defaultAvatar;

        if ($profileImagePath && file_exists(storage_path('app/public/' . $profileImagePath))) {
            $avatarImage = url(self::STORAGES . $profileImagePath);
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
            'review_count'            => $reviewCount,
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
    }

    public function formatVehicleInterest(VehicleInfo $vehicle): array
    {
        $images = $this->getVehicleImages($vehicle);
        $filteredPrices = $this->filterVehiclePrices($vehicle->vehicle_price);
        $wishlistExists = $this->checkWishlist($vehicle->id);
        $currencySymbol = $this->getCurrencySymbol();
        $rating = Review::where('vehicle_id', $vehicle->id)->value('average_ratings') ?? 0;
        $reviewCount = Review::where('vehicle_id', $vehicle->id)->count();
        $avatarImage = $this->getOwnerAvatar($vehicle);

        return [
            'id'                 => $vehicle->id,
            'name'               => $vehicle->name,
            'slug'               => $vehicle->slug,
            'vehicle_image'      => $images[0] ?? null,
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
            'review_count'       => $reviewCount,
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

    public function formatVehicleDetails(VehicleInfo $vehicle, bool $extraServiceEnabled, bool $faqEnabled): array
    {
        $images = $this->getVehicleMeta($vehicle->id, 'vehicle_image');
        $docs = $this->getVehicleMeta($vehicle->id, 'vehicle_doc');
        $policies = $this->getVehicleMeta($vehicle->id, 'vehicle_policy');

        $filteredPrices = $this->filterVehiclePrices($vehicle->vehicle_price);
        $multipleImages = $this->formatImages($images, $vehicle->vehicle_image);
        $documents = $this->urlizeArray($docs);
        $policyDocuments = $this->urlizeArray($policies);
        $currencySymbol = $this->getCurrencySymbol();
        $rating = Review::where('vehicle_id', $vehicle->id)->value('average_ratings') ?? 0;
        $reviewCount = Review::where('vehicle_id', $vehicle->id)->count();

        $extraServices = $extraServiceEnabled ? $this->formatExtraServices($vehicle->extraservices) : null;
        $faqs = $faqEnabled ? $vehicle->faqs->map(fn($f) => $f->only(['question', 'answer'])) : collect();

        return [
            'id'                      => $vehicle->id,
            'name'                    => $vehicle->name,
            'slug'                    => $vehicle->slug,
            'vehicle_image'           => $multipleImages[0] ?? null,
            'multiple_vehicle_images' => $multipleImages,
            'documents'               => $documents,
            'policies'                => $policyDocuments,
            'brand'                   => $vehicle->brand->brand_name ?? null,
            'car_type'                => $vehicle->carType->name ?? null,
            'category'                => $vehicle->category->name ?? null,
            'location'                => $vehicle->mainLocation->name ?? null,
            'color'                   => $vehicle->color->name ?? null,
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
            'review_count'            => $reviewCount,
            'price'                   => $filteredPrices,
            'is_featured'             => $vehicle->feature ?? 0,
            'is_top_rated'            => is_numeric($rating) && $rating >= 4,
            'seo_title'               => $vehicle->vehicle_metatitle,
            'seo_key'                 => $vehicle->vehicle_metakeywords,
            'seo_description'         => $vehicle->vehicle_metadesc,
            'authenticated'           => Auth::guard('web')->check(),
            'description'             => $vehicle->description,
            'extraservice'            => $extraServices,
            'tariff'                  => $vehicle->tariffs->map(fn($t) => $t->only([
                'tariff_title',
                'tariff_daily_price',
                'tariff_from_days',
                'tariff_to_days',
                'tariff_base_km',
                'tariff_extra_price',
            ])),
            'seasonal'                => $vehicle->seasonals->map(fn($s) => $s->only([
                'seasonal_title',
                'seasonal_start_date',
                'seasonal_end_date',
                'seasonal_daily_rate',
                'seasonal_weekly_rate',
                'seasonal_monthly_rate',
                'seasonal_late_fee',
            ])),
            'faqs'                    => $faqs,
            'damages'                 => $vehicle->damages->map(fn($d) => $d->only(['damage_type', 'damage_loaction', 'image', 'description'])),
            'owner_details'           => $this->formatOwner($vehicle),
        ];
    }

    private function getVehicleImages(VehicleInfo $vehicle): array
    {
        $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)->where('key', 'vehicle_image')->first();
        $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

        if (!empty($vehicle->vehicle_image)) {
            array_unshift($multipleImages, $vehicle->vehicle_image);
        }

        return array_map(fn($img) => url('storage' . str_replace(self::VEHICLE_IMAGE, self::VEHICLE_IMAGE_SMALL, '/' . ltrim($img, '/'))), $multipleImages);
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

    private function checkWishlist(int $vehicleId): bool
    {
        $authId = currentUser()->id ?? null;
        if (!$authId) {
            return false;
        }

        return Wishlist::where('user_id', $authId)
            ->where('vehicle_id', $vehicleId)
            ->exists();
    }

    private function getOwnerAvatar(VehicleInfo $vehicle): string
    {
        $defaultAvatar = asset('/backend/assets/img/default-profile.png');
        $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;

        if (!$profileImagePath) {
            return $defaultAvatar;
        }

        return uploadedAsset($profileImagePath, 'profile');
    }

    private function getVehicleMeta(int $vehicleId, string $key): array
    {
        $meta = VehicleMeta::where('vehicle_id', $vehicleId)->where('key', $key)->first();
        return $meta ? json_decode($meta->value, true) : [];
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

    private function urlizeArray(array $items): array
    {
        return array_map(fn($item) => url(self::STORAGE . $item), $items);
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
            'image'        => $vehicle->owner->userDetails ? url(self::STORAGES . $vehicle->owner->userDetails->profile_image) : null,
        ];
    }
}
