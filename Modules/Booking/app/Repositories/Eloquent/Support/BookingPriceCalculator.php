<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingPriceCalculator
{
    public function calculate(Request $request): array
    {
        $data = $request->all();

        $vehiclePrice = (float) ($data['vehicle_price'] ?? 0);
        $vehiclePriceType = strtolower($data['vehicle_price_type'] ?? 'daily');
        $driverPrice = (float) ($data['driver_price'] ?? 0);
        $securityDeposit = (float) ($data['security_deposit'] ?? 0);

        $startDate = Carbon::parse(($data['start_date'] ?? '') . ' ' . ($data['start_time'] ?? ''));
        $endDate = Carbon::parse(($data['end_date'] ?? '') . ' ' . ($data['end_time'] ?? ''));

        $noOfDays = $startDate->diffInDays($endDate) + 1;
        $noOfMonths = $startDate->diffInMonths($endDate) + 1;
        $noOfYears = $startDate->diffInYears($endDate) + 1;

        $vehiclePriceRate = $this->calculateVehicleRate($vehiclePriceType, $vehiclePrice, $noOfDays, $noOfMonths, $noOfYears);

        [$totalExtraServicePrice, $extraServiceIds] = $this->calculateServicePrice($data['extra_service'] ?? null, $vehiclePriceRate, $noOfDays);
        [$totalInsurancePrice, $insuranceIds] = $this->calculateServicePrice($data['insurance'] ?? null, $vehiclePriceRate, $noOfDays);

        $vehiclePriceRate = number_format($vehiclePriceRate, 2, '.', '');
        $driverPrice = number_format($driverPrice, 2, '.', '');
        $securityDeposit = number_format($securityDeposit, 2, '.', '');
        $totalExtraServicePrice = number_format($totalExtraServicePrice, 2, '.', '');
        $totalInsurancePrice = number_format($totalInsurancePrice, 2, '.', '');

        $totalPriceWithoutInsurance = number_format($driverPrice + $securityDeposit + $vehiclePriceRate + $totalExtraServicePrice, 2, '.', '');
        $totalPriceWithInsurance = number_format($driverPrice + $securityDeposit + $vehiclePriceRate + $totalExtraServicePrice + $totalInsurancePrice, 2, '.', '');

        return [
            'no_of_days'                => $noOfDays,
            'vehicle_price_rate'        => $vehiclePriceRate,
            'vehicle_price_type'        => $vehiclePriceType,
            'driver_price'              => $driverPrice,
            'security_deposit'          => $securityDeposit,
            'total_extra_service_price' => $totalExtraServicePrice,
            'total_extra_service'       => count($extraServiceIds),
            'total_price_val'           => $totalPriceWithoutInsurance,
            'total_price'               => $totalPriceWithInsurance,
            'total_insurance_price'     => $totalInsurancePrice,
            'total_insurance'           => count($insuranceIds),
            'insurance_name'            => implode(', ', array_filter($insuranceIds)),
            'extra_service_name'        => implode(', ', array_filter($extraServiceIds)),
        ];
    }

    private function calculateVehicleRate(string &$type, float $price, int $days, int $months, int $years): float
    {
        return match ($type) {
            'daily' => $days * $price,
            'weekly' => ceil($days / 7) * $price,
            'monthly' => $months * $price,
            'yearly' => $years * $price,
            default => $days * $price,
        };
    }

    private function calculateServicePrice(?string $jsonServices, float $vehicleRate, int $noOfDays): array
    {
        $totalPrice = 0;
        $serviceIds = [];

        if (!empty($jsonServices)) {
            $services = json_decode($jsonServices, true);
            if (is_array($services)) {
                foreach ($services as $service) {
                    $price = (float) ($service['price'] ?? 0);
                    $totalPrice += match ($service['type']) {
                        'per_day', 'daily' => $noOfDays * $price,
                        'one_time', 'fixed' => $price,
                        'percentage' => ($vehicleRate * $price) / 100,
                        default => 0,
                    };
                    $serviceIds[] = $service['id'] ?? null;
                }
            }
        }

        return [$totalPrice, $serviceIds];
    }
}
