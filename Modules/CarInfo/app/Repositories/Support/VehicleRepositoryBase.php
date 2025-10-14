<?php

namespace Modules\CarInfo\Repositories\Support;

use Illuminate\Http\Request;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;

abstract class VehicleRepositoryBase
{
    public const VEHICLE_IMAGE_PATH = 'vehicles/images';
    public const VEHICLE_IMAGE = 'vehicles/images/';
    public const VEHICLE_IMAGE_SMALL = 'vehicles/images/small/';
    public const STORAGE = 'storage/';
    public const STORAGES = '/storage/';
    public const DATE_FORMAT = 'm/d/Y';
    public const CAR_TYPE = 'carType:id,name';
    public const BRAND = 'brand:id,brand_name';
    public const CATEGORY = 'category:id,name';
    public const MAIN_LOCATION = 'mainLocation:id,name';
    public const COLOR = 'color:id,name,value';
    public const FUEL_TYPE = 'fuel_type:id,fuel_type';
    public const TRANSMISSION = 'transmission:id,name';

    protected function getCurrencySymbol(): string
    {
        $currencySetting = GeneralSetting::where('key', 'currency_symbol')->first();

        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
            if ($currency && $currency->symbol) {
                return $currency->symbol;
            }
        }

        return '$';
    }

    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @param class-string<TModel> $modelClass
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    protected function getActiveByLanguage(string $modelClass, ?int $languageId)
    {
        return $modelClass::where('status', 1)
            ->when($languageId, fn($q) => $q->where('language_id', $languageId))
            ->orderBy('id', 'desc')
            ->get();
    }

    protected function getLanguageIdFromRequest(Request $request): ?int
    {
        if ($request->has('language_id')) {
            $language = Language::find($request->query('language_id'));
            return $language->language_id ?? null;
        }

        return currentUser()->language_id ?? null;
    }

    protected function findVehicleBySlug(string $slug, ?int $languageId): ?VehicleInfo
    {
        $vehicle = VehicleInfo::where('slug', $slug)
            ->when($languageId, fn($q) => $q->where('language_id', $languageId))
            ->first();

        if (!$vehicle && $languageId) {
            $base = VehicleInfo::where('slug', $slug)->whereNull('parent_id')->first();
            if ($base) {
                return VehicleInfo::firstOrNew([
                    'parent_id'   => $base->id,
                    'language_id' => $languageId,
                ]);
            }
        }

        if (!$vehicle) {
            $base = VehicleInfo::where('slug', $slug)->first();
            if ($base) {
                return VehicleInfo::firstOrNew([
                    'parent_id'   => $base->parent_id,
                    'language_id' => $languageId,
                ]);
            }
        }

        return $vehicle;
    }

    protected function getAuthenticatedLanguageId(): int
    {
        $authUser = currentUser();
        return $authUser->language_id ?? 1;
    }

    protected function isSettingEnabled(int $groupId, string $key): bool
    {
        return (bool) GeneralSetting::where('group_id', $groupId)->where('key', $key)->value('value');
    }

    protected function errorResponse(string $message, int $code): array
    {
        return [
            'code'    => $code,
            'success' => false,
            'message' => $message,
            'data'    => [],
        ];
    }
}
