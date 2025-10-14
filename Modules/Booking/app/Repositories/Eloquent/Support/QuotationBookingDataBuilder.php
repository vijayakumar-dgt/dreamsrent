<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;

class QuotationBookingDataBuilder
{
    public function parseDateTimes(Request $request): array
    {
        $startDate = $request->input('start_date');
        $startTime = $request->input('start_time');
        $endDate = $request->input('end_date');
        $endTime = $request->input('end_time');

        if (!is_string($startDate) || !is_string($startTime) || !is_string($endDate) || !is_string($endTime)) {
            throw new \InvalidArgumentException('Invalid date or time input.');
        }

        $startDateTime = Carbon::parse($startDate . ' ' . $startTime)->format('Y-m-d H:i:s');
        $endDateTime = Carbon::parse($endDate . ' ' . $endTime)->format('Y-m-d H:i:s');

        return [$startDateTime, $endDateTime];
    }

    public function prepareBookingData(Request $request, string $startDateTime, string $endDateTime): array
    {
        return [
            'vehicle_id'                => $request->vehicle_id,
            'customer_id'               => $request->customer_id,
            'booking_by'                => 'quotation',
            'driver_id'                 => $request->driver_id ?? null,
            'driver_price'              => $request->driver_price ?? 0,
            'vehicle_price'             => $request->vehicle_price,
            'total_insurance_price'     => $request->total_insurance_price ?? 0,
            'total_extra_service_price' => $request->total_extra_service_price ?? 0,
            'final_price'               => $request->final_price ?? 0,
            'extra_service'             => $request->extra_service ?? null,
            'insurance'                 => $request->insurance ?? null,
            'security_deposit'          => $request->security_deposit ?? null,
            'start_datetime'            => $startDateTime,
            'end_datetime'              => $endDateTime,
            'pickup_location'           => $request->pickup_location,
            'return_location'           => $request->return_location,
            'booking_status'            => 1,
            'booking_tariff'            => $request->tariff ?? null,
            'driving_type'              => $request->driving_type ?? null,
            'rental_type'               => $request->vehicle_price_type ?? null,
            'no_of_passengers'          => $request->no_of_passengers ?? null,
            'no_of_days'                => $request->no_of_days ?? null,
            'vehicle_total_price'       => $request->vehicle_total_price ?? null,
            'base_km'                   => $request->base_km ?? null,
            'km_extra_price'            => $request->km_extra_price ?? null,
            'expenses'                  => $request->expenses ?? null,
            'delivery_price'            => $request->delivery_price ?? null,
            'tax_val'                   => $request->tax_val ?? null,
            'tax_type'                  => $request->tax_type ?? null,
            'booking_date'              => now(),
        ];
    }

    public function prepareBookingDetails(Request $request): array
    {
        $details = [
            'vehicle_price_type' => $request->vehicle_price_type,
            'vehicle_season_id'  => $request->vehicle_season_id ?? null,
            'vehicle_tariff_id'  => $request->vehicle_tariff_id ?? null,
        ];

        if ($request->vehicle_tariff_id) {
            $vehicleTariff = VehicleTarrif::find($request->vehicle_tariff_id);
            if ($vehicleTariff instanceof VehicleTarrif) {
                $details = array_merge($details, [
                    'tariff_title'       => $vehicleTariff->tariff_title,
                    'tariff_price'       => $vehicleTariff->tariff_daily_price,
                    'tariff_from_days'   => $vehicleTariff->tariff_from_days,
                    'tariff_to_days'     => $vehicleTariff->tariff_to_days,
                    'tariff_base_km'     => $vehicleTariff->tariff_base_km,
                    'tariff_extra_price' => $vehicleTariff->tariff_extra_price,
                ]);
            }
        }

        if ($request->vehicle_season_id) {
            $vehicleSeason = VehicleSeason::find($request->vehicle_season_id);
            if ($vehicleSeason instanceof VehicleSeason) {
                $details = array_merge($details, [
                    'seasonal_title'        => $vehicleSeason->seasonal_title,
                    'seasonal_start_date'   => $vehicleSeason->seasonal_start_date,
                    'seasonal_end_date'     => $vehicleSeason->seasonal_end_date,
                    'seasonal_daily_rate'   => $vehicleSeason->seasonal_daily_rate,
                    'seasonal_weekly_rate'  => $vehicleSeason->seasonal_weekly_rate,
                    'seasonal_monthly_rate' => $vehicleSeason->seasonal_monthly_rate,
                    'seasonal_late_fee'     => $vehicleSeason->seasonal_late_fee,
                ]);
            }
        }

        return $details;
    }
}
