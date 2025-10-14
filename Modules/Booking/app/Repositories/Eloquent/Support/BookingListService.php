<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;

class BookingListService
{
    public function buildBookingQuery(Request $request): Builder
    {
        $query = Booking::select(
            'bookings.id',
            'bookings.reservation_id',
            BookingQueryConfig::VEHICLE_NAME_SELECT,
            'vehicle_info.vehicle_image',
            \DB::raw(BookingQueryConfig::CUSTOMER_FULLNAME_SELECT),
            BookingQueryConfig::CUSTOMER_IMAGE_SELECT,
            'users.id as customer_id',
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
            ->join(BookingQueryConfig::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join(BookingQueryConfig::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->where('bookings.booking_by', '!=', 'quotation');

        $this->applyBookingFilters($query, $request);
        $this->applyBookingSort($query, $request);

        return $query;
    }

    public function getTotalBookingCount(): int
    {
        return Booking::join('users', 'users.id', '=', 'bookings.customer_id')
            ->where('bookings.booking_by', '!=', 'quotation')
            ->count();
    }

    public function formatBookingData($bookings)
    {
        return $bookings->map(function ($booking) {
            $imagePath = $booking->vehicle_image;
            $filename = basename($imagePath);
            $newPath = BookingQueryConfig::VEHICLE_IMAGE_PATH . $filename;
            $file = public_path(BookingQueryConfig::STORAGE_PATH . $newPath);
            if (file_exists($file)) {
                $imagePath = $newPath;
            }
            $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
            $booking->vehicle_image = uploadedAsset($imagePath);
            $booking->booking_status_text = is_numeric($booking->booking_status)
                ? Booking::getStatusLabel((int) $booking->booking_status)
                : null;
            $booking->customer_full_name = ucwords($booking->customer_full_name);

            return $booking;
        });
    }

    private function applyBookingFilters(Builder $query, Request $request): void
    {
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bookings.reservation_id', 'LIKE', "%{$search}%")
                    ->orWhere('vehicle_info.name', 'LIKE', "%{$search}%")
                    ->orWhere('users.name', 'LIKE', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->whereIn('bookings.booking_status', (array) $status);
        }

        if ($pickup = $request->input('pickup_location_ids')) {
            $query->whereIn('bookings.pickup_location', (array) $pickup);
        }

        if ($drop = $request->input('drop_location_ids')) {
            $query->whereIn('bookings.return_location', (array) $drop);
        }

        if ($dateRange = $request->input('sort_by_date')) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $start = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))?->startOfDay();
                $end = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))?->endOfDay();
                if ($start && $end) {
                    $query->whereBetween('bookings.created_at', [$start, $end]);
                }
            }
        }
    }

    private function applyBookingSort(Builder $query, Request $request): void
    {
        $sort = strtolower($request->input('sort_by', 'latest'));

        switch ($sort) {
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
                $start = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                $end = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                $query->whereBetween('bookings.created_at', [$start, $end]);
                break;
            case 'last 7 days':
                $start = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                $end = \Carbon\Carbon::now()->endOfDay();
                $query->whereBetween('bookings.created_at', [$start, $end]);
                break;
            default:
                $query->orderBy('bookings.created_at', 'desc');
        }

        $columns = ['id', 'reservation_id', 'vehicle_name', 'customer_full_name', 'pickup_location', 'drop_location', 'booking_status'];
        $orderBy = $columns[$request->input('order.0.column', 0)] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderBy, $orderDir);
    }
}
