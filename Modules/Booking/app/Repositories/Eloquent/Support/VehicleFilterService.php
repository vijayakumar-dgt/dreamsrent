<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;

class VehicleFilterService
{
    public function filterVehicles(Request $request, string $vehicleImagePath, string $storagePath): LengthAwarePaginator
    {
        $filters = $this->extractFilters($request);

        $vehicles = $this->buildVehicleQuery($filters)
            ->orderBy('vehicle_info.id', $filters['orderBy'])
            ->paginate($filters['perPage'], ['*'], 'page', $filters['page']);

        $vehicles->getCollection()->transform(function ($vehicle) use ($vehicleImagePath, $storagePath) {
            return $this->formatVehicle($vehicle, $vehicleImagePath, $storagePath);
        });

        return $vehicles;
    }

    private function extractFilters(Request $request): array
    {
        $filters = [
            'orderBy'        => $request->order_by ?? 'desc',
            'search'         => $request->search ?? null,
            'perPage'        => $request->per_page ?? 10,
            'page'           => $request->page ?? 1,
            'tariff'         => $request->tariff ?? null,
            'brandIds'       => $request->brand_ids ?? null,
            'modelIds'       => $request->model_ids ?? null,
            'typeIds'        => $request->type_ids ?? null,
            'colorIds'       => $request->color_ids ?? null,
            'pickupLocation' => $request->pickup_location ?? null,
            'returnLocation' => $request->return_location ?? null,
            'bookingId'      => $request->booking_id ?? null,
        ];

        [$filters['startDateTime'], $filters['startDateFormat']] = $this->parseDateTime(
            $request->start_date ?? null,
            $request->start_time ?? null
        );

        [$filters['endDateTime'], $filters['endDateFormat']] = $this->parseDateTime(
            $request->end_date ?? null,
            $request->end_time ?? null
        );

        return $filters;
    }

    private function parseDateTime(?string $date, ?string $time): array
    {
        if (empty($date) || !is_string($date)) {
            return ['', ''];
        }

        $dateTimeString = $date . ($time ? " {$time}" : '');
        $dateFormat = $time ? BookingQueryConfig::DISPLAY_DATE_FORMAT : 'd-m-Y';

        $carbon = Carbon::createFromFormat($dateFormat, $dateTimeString);
        $carbonOnly = Carbon::createFromFormat('d-m-Y', $date);

        return [
            $carbon ? $carbon->format(BookingQueryConfig::DB_DATE_FORMAT) : '',
            $carbonOnly ? $carbonOnly->format('Y-m-d') : ''
        ];
    }

    private function buildVehicleQuery(array $filters): Builder
    {
        $query = VehicleInfo::select(
            'vehicle_info.id',
            'vehicle_info.vehicle_image as image',
            BookingQueryConfig::VEHICLE_NAME_SELECT,
            'vehicle_info.year',
            'cartypes.name as vehicle_type',
            'brands.brand_name',
            'car_models.model_name',
            'car_colors.name as color_name',
            'car_colors.value as color_value',
        )
            ->join('cartypes', 'vehicle_info.type_id', '=', 'cartypes.id')
            ->join('brands', 'vehicle_info.brand_id', '=', 'brands.id')
            ->join('car_models', 'vehicle_info.model_id', '=', 'car_models.id')
            ->join('car_colors', 'vehicle_info.color_id', '=', 'car_colors.id')
            ->leftJoin('bookings', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->distinct();

        $this->filterByMaintenance($query, $filters);
        $this->filterByVehicleAttributes($query, $filters);
        $this->filterByLocations($query, $filters);
        $this->filterBySearchTerm($query, $filters);
        $this->filterByTariff($query, $filters);
        $this->filterByAvailability($query, $filters);

        return $query;
    }

    private function filterByMaintenance(Builder $query, array $filters): void
    {
        $query->whereDoesntHave('maintenances', function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('maintenances.start_date', '<=', $filters['endDateFormat'])
                    ->where('maintenances.end_date', '>=', $filters['startDateFormat'])
                    ->where('maintenances.status', '!=', 3);
            });
        });
    }

    private function filterByVehicleAttributes(Builder $query, array $filters): void
    {
        $query->when(!empty($filters['brandIds']), fn($q) => $q->whereIn('vehicle_info.brand_id', $filters['brandIds']))
            ->when(!empty($filters['typeIds']), fn($q) => $q->whereIn('vehicle_info.type_id', $filters['typeIds']))
            ->when(!empty($filters['modelIds']), fn($q) => $q->whereIn('vehicle_info.model_id', $filters['modelIds']))
            ->when(!empty($filters['colorIds']), fn($q) => $q->whereIn('vehicle_info.color_id', $filters['colorIds']));
    }

    private function filterByLocations(Builder $query, array $filters): void
    {
        $query->when(!empty($filters['pickupLocation']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('vehicle_info.main_location_id', '=', $filters['pickupLocation'])
                    ->orWhereJsonContains('vehicle_info.other_location_id', (string) $filters['pickupLocation']);
            });
        });

        $query->when(!empty($filters['returnLocation']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('vehicle_info.main_location_id', '=', $filters['returnLocation'])
                    ->orWhereJsonContains('vehicle_info.other_location_id', (string) $filters['returnLocation']);
            });
        });
    }

    private function filterBySearchTerm(Builder $query, array $filters): void
    {
        $query->when($filters['search'], function ($query) use ($filters) {
            $search = (string) $filters['search'];
            $tariff = (string) ($filters['tariff'] ?? '');
            $path = '$[0].' . $filters['tariff'];

            $query->where(function ($q) use ($search) {
                $q->where('vehicle_info.year', 'LIKE', "%{$search}%")
                    ->orWhere('vehicle_info.name', 'LIKE', "%{$search}%")
                    ->orWhere('brands.brand_name', 'LIKE', "%{$search}%")
                    ->orWhere('car_models.model_name', 'LIKE', "%{$search}%")
                    ->orWhere('cartypes.name', 'LIKE', "%{$search}%")
                    ->orWhere('car_colors.name', 'LIKE', "%{$search}%");
            });

            if (filled($tariff)) {
                $query->orWhereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, ?)) LIKE ?",
                    [$path, "%{$search}%"]
                );
            }
        });
    }

    private function filterByTariff(Builder $query, array $filters): void
    {
        if ($filters['tariff']) {
            $this->applySpecificTariff($query, $filters);
            return;
        }

        $this->applyDefaultTariff($query, $filters);
    }

    private function applySpecificTariff(Builder $query, array $filters): void
    {
        $noOfDays = $this->calculateNoOfDays($filters);

        $query->leftJoin('vehicle_tarrifs', function ($join) use ($noOfDays) {
            $join->on('vehicle_tarrifs.vehicle_id', '=', 'vehicle_info.id')
                ->whereRaw('CAST(vehicle_tarrifs.tariff_from_days AS UNSIGNED) <= ?', [$noOfDays])
                ->whereRaw('CAST(vehicle_tarrifs.tariff_to_days AS UNSIGNED) >= ?', [$noOfDays]);
        })
            ->where(function ($q) use ($filters) {
                $q->whereNotNull('vehicle_tarrifs.tariff_daily_price')
                    ->orWhereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, '$[0].{$filters['tariff']}')) IS NOT NULL"
                    );
            })
            ->selectRaw(
                "
                vehicle_tarrifs.id as vehicle_tariff_id,
                COALESCE(
                    vehicle_tarrifs.tariff_daily_price,
                    JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, '$[0].{$filters['tariff']}'))
                ) as vehicle_price,
                ? as vehicle_price_type
                ",
                [$filters['tariff']]
            );
    }

    private function applyDefaultTariff(Builder $query, array $filters): void
    {
        $noOfDays = $this->calculateNoOfDays($filters);
        $start = !empty($filters['startDateTime']) ? Carbon::parse($filters['startDateTime']) : Carbon::now();
        $daysInMonth = $start->daysInMonth;

        $tariffType = 'daily';
        $seasonalRateColumn = 'seasonal_daily_rate';

        if ($noOfDays >= 7 && $noOfDays < $daysInMonth) {
            $tariffType = 'weekly';
            $seasonalRateColumn = 'seasonal_weekly_rate';
        } elseif ($noOfDays >= $daysInMonth && $noOfDays < 365) {
            $tariffType = 'monthly';
            $seasonalRateColumn = 'seasonal_monthly_rate';
        } elseif ($noOfDays >= 365) {
            $tariffType = 'yearly';
        }

        $jsonPath = "$[0].$tariffType";
        $end = !empty($filters['endDateTime']) ? Carbon::parse($filters['endDateTime']) : Carbon::now();

        $query->leftJoin('vehicle_seasons', function ($join) use ($start, $end) {
            $join->on('vehicle_seasons.vehicle_id', '=', 'vehicle_info.id')
                ->where(function ($q) use ($start, $end) {
                    $q->whereDate('vehicle_seasons.seasonal_start_date', '<=', $start)
                        ->whereDate('vehicle_seasons.seasonal_end_date', '>=', $end);
                });
        })
            ->where(function ($q) use ($seasonalRateColumn, $jsonPath) {
                $q->whereNotNull("vehicle_seasons.$seasonalRateColumn")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, ?)) IS NOT NULL", [$jsonPath]);
            })
            ->selectRaw(
                "
                vehicle_seasons.id as vehicle_season_id,
                COALESCE(
                    vehicle_seasons.$seasonalRateColumn,
                    JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, ?))
                ) as vehicle_price,
                COALESCE(
                    CASE
                        WHEN vehicle_seasons.$seasonalRateColumn IS NOT NULL THEN ?
                        ELSE ?
                    END, ?
                ) as vehicle_price_type
                ",
                [
                    $jsonPath,
                    $tariffType,
                    $tariffType,
                    $tariffType,
                ]
            );
    }

    private function filterByAvailability(Builder $query, array $filters): void
    {
        $bookingId = $filters['bookingId'] ?? null;

        $query->when(!empty($filters['startDateTime']) || !empty($filters['endDateTime']), function ($query) use ($filters, $bookingId) {
            $query->whereNotExists(function ($q) use ($filters, $bookingId) {
                $q->select(DB::raw(1))
                    ->from('bookings')
                    ->whereRaw('bookings.vehicle_id = vehicle_info.id')
                    ->where(function ($q) use ($filters) {
                        $q->where(function ($q) use ($filters) {
                            $q->whereBetween('bookings.start_datetime', [$filters['startDateTime'], $filters['endDateTime']])
                                ->orWhereBetween('bookings.end_datetime', [$filters['startDateTime'], $filters['endDateTime']])
                                ->orWhere(function ($q) use ($filters) {
                                    $q->where('bookings.start_datetime', '<=', $filters['startDateTime'])
                                        ->where('bookings.end_datetime', '>=', $filters['endDateTime']);
                                });
                        })
                            ->whereNotIn('bookings.booking_status', [6, 3]);
                    });

                if (!empty($bookingId)) {
                    $q->where('bookings.id', '!=', $bookingId);
                }
            });
        });
    }

    private function calculateNoOfDays(array $filters): int
    {
        if (!empty($filters['startDateTime']) && !empty($filters['endDateTime'])) {
            $start = Carbon::parse($filters['startDateTime']);
            $end = Carbon::parse($filters['endDateTime']);
            return (int) ceil($start->diffInMinutes($end) / 1440);
        }

        return 1;
    }

    private function formatVehicle($vehicle, string $vehicleImagePath, string $storagePath)
    {
        $vehicleImage = $vehicle->image ?? '';
        $filename = basename($vehicleImage);
        $newPath = $vehicleImagePath . $filename;
        $file = public_path($storagePath . $newPath);

        if (file_exists($file)) {
            $vehicleImage = $newPath;
        }

        $vehicle->image = uploadedAsset($vehicleImage);
        $vehicle->vehicle_price = number_format((float) $vehicle->vehicle_price, 2, '.', '');
        $vehicle->encrypted_id = customEncrypt($vehicle->id, Booking::$reservationSecretKey);

        return $vehicle;
    }
}
