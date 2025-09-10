<?php

namespace Modules\Booking\Services;

use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleInsurance;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\TaxGroup;

class VehicleService
{
    public function getVehicleBySlug(string $slug)
    {
        return VehicleInfo::select(
            'id', 'name', 'slug', 'vehicle_image',
            'main_location_id', 'other_location_id',
            'vehicle_price', 'passenger_capacity'
        )
            ->where('slug', $slug)
            ->first();
    }

    public function getFilteredPrices($vehicle): array
    {
        $prices = $vehicle && is_string($vehicle->vehicle_price)
            ? (is_array($decodedPrice = json_decode($vehicle->vehicle_price, true)) ? ($decodedPrice[0] ?? []) : [])
            : [];

        return array_filter(is_array($prices) ? $prices : [], fn($price) => $price > 0);
    }

    public function getMainLocation($vehicle)
    {
        return $vehicle?->main_location_id
            ? Location::select('name', 'address')->where('id', $vehicle->main_location_id)->first()
            : null;
    }

    public function getAllLocations($vehicle)
    {
        $allLocation = collect();

        if ($vehicle?->main_location_id) {
            $mainLocation = Location::select('id', 'name', 'address')
                ->where('id', $vehicle->main_location_id)
                ->first();

            if ($mainLocation) {
                $allLocation->push($mainLocation);
            }
        }

        if (!empty($vehicle->other_location_id)) {
            $otherIds = json_decode($vehicle->other_location_id, true);

            if (is_array($otherIds)) {
                $filteredOtherIds = array_filter($otherIds, fn($id) => $id != $vehicle->main_location_id);

                if (!empty($filteredOtherIds)) {
                    $otherLocations = Location::select('id', 'name', 'address')
                        ->whereIn('id', $filteredOtherIds)
                        ->get();

                    $allLocation = $allLocation->merge($otherLocations);
                }
            }
        }

        return $allLocation;
    }

    public function getPickupAndDeliveryLocations($request): array
    {
        return [
            'dlocation'  => Location::select('id', 'name', 'address')->where('id', $request->delivery_location)->first(),
            'plocation'  => Location::select('id', 'name', 'address')->where('id', $request->pickup_location)->first(),
            'rlocation'  => Location::select('id', 'name', 'address')->where('id', $request->delivery_return_location)->first(),
            'prlocation' => Location::select('id', 'name', 'address')->where('id', $request->pickup_return_location)->first(),
        ];
    }

    public function getExtraServices(?int $vehicleId)
    {
        return VehicleExtraService::with(['extraService:id,name,icon,description'])
            ->select("extra_service_id", "value", "price")
            ->where('vehicle_id', $vehicleId)
            ->get()
            ->map(function ($service) {
                $service->extraService->icon = uploadedAsset($service->extraService->icon ?? '');
                return $service;
            });
    }

    public function getCurrencySymbol(): string
    {
        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
            return $currency->symbol ?? "$";
        }
        return "$";
    }

    public function getVehicleInsurances(?int $vehicleId)
    {
        $vehicleInsurance = VehicleInsurance::select(
            'vehicle_insurances.insurances_id',
            'vehicle_insurances.value',
            'vehicle_insurances.price',
            'insurances.insurance_name',
            'insurances.price as insurance_price',
            'insurances.price_type_id',
            'insurances.status'
        )
            ->join('insurances', 'vehicle_insurances.insurances_id', '=', 'insurances.id')
            ->with(['insuranceBenefits' => fn($query) => $query->select('insurance_id', 'benefit')])
            ->where('vehicle_insurances.vehicle_id', $vehicleId)
            ->get();

        return $vehicleInsurance->transform(function ($insurance) {
            $insurance->benefits_count = $insurance->insuranceBenefits->count();
            $insurance->first_benefit  = $insurance->insuranceBenefits->first()->benefit ?? 'No benefits available';
            return $insurance;
        });
    }

    public function getDriverInfo(?int $vehicleId)
    {
        return Driver::select("id", "driver_name", "image")
            ->where("assigned_cars", $vehicleId)
            ->first();
    }

    public function calculateTaxes(float $finalRate): array
    {
        $taxGroups = TaxGroup::with(['taxRates'])->get();
        $calculatedTaxes = [];

        foreach ($taxGroups as $group) {
            foreach ($group->taxRates as $rate) {
                $taxAmount = ($rate->tax_rate / 100) * $finalRate;
                $calculatedTaxes[] = [
                    'group_name'   => $group->tax_name,
                    'rate_name'    => $rate->tax_name,
                    'rate_percent' => $rate->tax_rate,
                    'amount'       => $taxAmount,
                ];
            }
        }
        return $calculatedTaxes;
    }

    public function getPaymentStatus(string $key): int
    {
        $setting = GeneralSetting::where("key", $key)->first();
        return ($setting && $setting->value == 1) ? 1 : 0;
    }
}
