<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Repositories\Eloquent\QuotationRepository;

class QuotationBookingQueryBuilder
{
    public function baseBookingQuery(): Builder
    {
        return Booking::select(
            'bookings.id',
            'bookings.reservation_id',
            'bookings.booking_date',
            QuotationRepository::VEHICLE_NAME_SELECT,
            'vehicle_info.vehicle_image',
            DB::raw(QuotationRepository::CUSTOMER_FULLNAME_SELECT),
            QuotationRepository::CUSTOMER_IMAGE_SELECT,
            'users.name as user_name',
            'bookings.start_datetime',
            'bookings.end_datetime',
            'pickup_location.name as pickup_location',
            'drop_location.name as drop_location',
            'bookings.booking_status',
            'bookings.booking_by'
        )
            ->join('users', 'users.id', '=', 'bookings.customer_id')
            ->leftJoin('user_details', 'user_details.user_id', '=', 'users.id')
            ->join(QuotationRepository::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join(QuotationRepository::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->where('bookings.booking_by', 'quotation');
    }

    public function applyFilters(Builder $query, Request $request): void
    {
        $this->applySearch($query, $request->search ?? null);
        $this->applyStatusFilter($query, $request->status ?? null);
        $this->applyLocationFilters($query, $request->pickup_location_ids ?? [], $request->drop_location_ids ?? []);
        $this->applyDateFilter($query, $request->sort_by_date ?? null);
        $this->applySortByDateRange($query, $request->sort_by ?? null);
    }

    public function applySorting(Builder $query, Request $request): void
    {
        $columns = ['id', 'reservation_id', 'vehicle_name', 'customer_full_name', 'pickup_location', 'drop_location', 'booking_status'];
        $orderBy = $columns[$request->input('order.0.column', 0)] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderBy, $orderDir);
    }

    public function findBookingForModal(int $bookingId): ?Booking
    {
        return Booking::select(
            'bookings.*',
            QuotationRepository::VEHICLE_NAME_SELECT,
            'vehicle_info.vehicle_image',
            DB::raw(QuotationRepository::CUSTOMER_FULLNAME_SELECT),
            QuotationRepository::CUSTOMER_IMAGE_SELECT,
            'users.name as user_name',
            'pickup_location.name as pickup_location_name',
            'drop_location.name as drop_location_name'
        )
            ->join('users', 'users.id', '=', 'bookings.customer_id')
            ->join('user_details', 'user_details.user_id', '=', 'users.id')
            ->join(QuotationRepository::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join(QuotationRepository::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->where('bookings.id', $bookingId)
            ->first();
    }

    public function findBookingWithDetails(int $bookingId): Booking
    {
        return Booking::select(
            'bookings.id',
            'bookings.reservation_id',
            'bookings.vehicle_id',
            'bookings.booking_status',
            'bookings.booking_date',
            'bookings.start_datetime',
            'bookings.end_datetime',
            'bookings.no_of_days',
            'bookings.driving_type',
            'bookings.pickup_location',
            'bookings.return_location',
            'bookings.security_deposit',
            'bookings.customer_id',
            'bookings.driver_id',
            'bookings.insurance',
            'bookings.extra_service',
            'bookings.driver_price',
            'bookings.vehicle_price',
            'bookings.vehicle_total_price',
            'bookings.total_insurance_price',
            'bookings.total_extra_service_price',
            'bookings.final_price',
            QuotationRepository::VEHICLE_NAME_SELECT,
            'vehicle_info.vehicle_image',
            'cartypes.name as vehicle_type',
            'pickup_location.name as pickup_location_name',
            'drop_location.name as drop_location_name',
            DB::raw(QuotationRepository::CUSTOMER_FULLNAME_SELECT),
            QuotationRepository::CUSTOMER_IMAGE_SELECT,
            'users.name as customer_user_name',
            'users.phone_number as customer_phone_number',
            'drivers.driver_name',
            'drivers.image as driver_image',
            'drivers.phone_number as driver_phone_number',
            'booking_details.vehicle_price_type',
            'bookings.rental_type',
            'bookings.delivery_type',
            'bookings.booking_by',
            'driving_types.name as driving_type_name'
        )
            ->leftJoin('booking_details', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'users.id', '=', 'bookings.customer_id')
            ->leftJoin('user_details', 'user_details.user_id', '=', 'users.id')
            ->join(QuotationRepository::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join(QuotationRepository::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->leftJoin('cartypes', 'cartypes.id', '=', 'vehicle_info.type_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'bookings.driver_id')
            ->leftJoin('driving_types', 'driving_types.id', '=', 'bookings.driving_type')
            ->where('bookings.id', $bookingId)
            ->firstOrFail();
    }

    private function applySearch(Builder $query, ?string $search): void
    {
        if (empty($search)) {
            return;
        }

        $query->where(function (Builder $q) use ($search) {
            $q->where('bookings.reservation_id', 'LIKE', "%{$search}%")
                ->orWhere('vehicle_info.name', 'LIKE', "%{$search}%")
                ->orWhere('users.name', 'LIKE', "%{$search}%");
        });
    }

    private function applyStatusFilter(Builder $query, $status): void
    {
        if (empty($status)) {
            return;
        }

        $query->whereIn('bookings.booking_status', (array) $status);
    }

    private function applyLocationFilters(Builder $query, array $pickupIds, array $dropIds): void
    {
        if (!empty($pickupIds)) {
            $query->whereIn('bookings.pickup_location', $pickupIds);
        }

        if (!empty($dropIds)) {
            $query->whereIn('bookings.return_location', $dropIds);
        }
    }

    private function applyDateFilter(Builder $query, ?string $sortByDate): void
    {
        if (!$sortByDate) {
            return;
        }

        $dates = explode(' - ', $sortByDate);
        if (count($dates) === 2) {
            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]));
            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]));

            if ($startDate && $endDate) {
                $query->whereBetween('bookings.created_at', [
                    $startDate->startOfDay(),
                    $endDate->endOfDay(),
                ]);
            }
        }
    }

    private function applySortByDateRange(Builder $query, ?string $sortBy): void
    {
        if (!$sortBy) {
            $query->orderBy('bookings.created_at', 'desc');
            return;
        }

        $sortBy = strtolower($sortBy);
        $now = Carbon::now();

        switch ($sortBy) {
            case 'latest':
                $query->orderBy('bookings.created_at', 'desc');
                break;
            case 'ascending':
                $query->orderBy('bookings.reservation_id', 'asc');
                break;
            case 'descending':
                $query->orderBy('bookings.reservation_id', 'desc');
                break;
            case 'last month':
                $query->whereBetween('bookings.created_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()]);
                break;
            case 'last 7 days':
                $query->whereBetween('bookings.created_at', [$now->copy()->subDays(7)->startOfDay(), $now->endOfDay()]);
                break;
            default:
                $query->orderBy('bookings.created_at', 'desc');
                break;
        }
    }
}
