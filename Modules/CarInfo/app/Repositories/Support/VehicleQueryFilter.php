<?php

namespace Modules\CarInfo\Repositories\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;

class VehicleQueryFilter extends VehicleRepositoryBase
{
    public function applyAdminFilters($query, Request $request): void
    {
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

        if (!is_null($request->sort_by_date)) {
            $this->applyDateFilter($query, $request->sort_by_date);
        }
    }

    public function applyAdminSorting($query, Request $request): void
    {
        $sortBy = $request->sort_by ?? 'ascending';

        match ($sortBy) {
            'latest'      => $query->orderBy('created_at', 'desc'),
            'ascending'   => $query->orderBy('name', 'asc'),
            'descending'  => $query->orderBy('name', 'desc'),
            'last_month'  => $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]),
            'last_7_days' => $query->where('created_at', '>=', now()->subDays(7)),
            default       => $query->orderBy('name', 'asc'),
        };
    }

    public function applyCustomerSorting($query, Request $request): void
    {
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
                $query->orderBy('created_at', 'desc');
                break;
        }
    }

    public function applyDateFilter($query, string $dateRange): void
    {
        $dates = explode(' - ', $dateRange);
        if (count($dates) !== 2) {
            return;
        }

        try {
            $startDate = Carbon::createFromFormat(self::DATE_FORMAT, trim($dates[0]))->startOfDay();
            $endDate = Carbon::createFromFormat(self::DATE_FORMAT, trim($dates[1]))->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } catch (\Exception) {
            // Ignore invalid date formats
        }
    }
}
