<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\ExtraService;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;

class BookingDetailService
{
    public function fetchBookingDetails(int $bookingId)
    {
        return Booking::select(
            'bookings.*',
            BookingQueryConfig::VEHICLE_NAME_SELECT,
            'vehicle_info.vehicle_image',
            DB::raw(BookingQueryConfig::CUSTOMER_FULLNAME_SELECT),
            BookingQueryConfig::CUSTOMER_IMAGE_SELECT,
            'users.name as user_name',
            'pickup_location.name as pickup_location_name',
            'drop_location.name as drop_location_name',
        )
            ->join('users', 'users.id', '=', 'bookings.customer_id')
            ->join('user_details', 'user_details.user_id', '=', 'users.id')
            ->join(BookingQueryConfig::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join(BookingQueryConfig::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->where('bookings.id', $bookingId)
            ->first();
    }

    public function formatBookingForDetails($booking): void
    {
        if (!$booking) {
            return;
        }

        $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
        $vehicleImagePath = $booking->vehicle_image ?? '';
        $filename = basename($vehicleImagePath);
        $newPath = BookingQueryConfig::VEHICLE_IMAGE_PATH . $filename;
        $file = public_path(BookingQueryConfig::STORAGE_PATH . $newPath);
        if (file_exists($file)) {
            $vehicleImagePath = $newPath;
        }
        $booking->vehicle_image = uploadedAsset($vehicleImagePath);

        if ($booking->insurance) {
            $booking->insurance_formatted = json_decode($booking->insurance);
        }

        if ($booking->extra_service) {
            $booking->extra_service_formatted = json_decode($booking->extra_service);
        }
        $booking->booking_status_text = Booking::getStatusLabel((int) $booking->booking_status);
    }

    public function getReservationData(int $bookingId): array
    {
        $booking = Booking::select(
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
            'bookings.tax_val',
            BookingQueryConfig::VEHICLE_NAME_SELECT,
            'vehicle_info.vehicle_image',
            'cartypes.name as vehicle_type',
            'pickup_location.name as pickup_location_name',
            'drop_location.name as drop_location_name',
            DB::raw(BookingQueryConfig::CUSTOMER_FULLNAME_SELECT),
            BookingQueryConfig::CUSTOMER_IMAGE_SELECT,
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
            ->join(BookingQueryConfig::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join(BookingQueryConfig::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->leftJoin('cartypes', 'cartypes.id', '=', 'vehicle_info.type_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'bookings.driver_id')
            ->leftJoin('driving_types', 'driving_types.id', '=', 'bookings.driving_type')
            ->where('bookings.id', $bookingId)
            ->firstOrFail();

        $this->formatReservationImages($booking);
        $this->formatExtraServices($booking);
        $this->formatInsurances($booking);
        $this->formatMisc($booking);

        $bookingHistories = BookingHistory::where('booking_id', $bookingId)->get([
            'id',
            'booking_id',
            'created_at',
            'message',
        ]);

        return [
            'booking'          => $booking,
            'bookingHistories' => $bookingHistories,
        ];
    }

    private function formatReservationImages(object $booking): void
    {
        $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
        $booking->driver_image = uploadedAsset($booking->driver_image, 'profile');

        $vehicleImagePath = $booking->vehicle_image ?? '';
        $filename = basename($vehicleImagePath);
        $newPath = BookingQueryConfig::VEHICLE_IMAGE_PATH . $filename;
        $file = public_path(BookingQueryConfig::STORAGE_PATH . $newPath);

        if (file_exists($file)) {
            $vehicleImagePath = $newPath;
        }

        $booking->vehicle_image = uploadedAsset($vehicleImagePath);
    }

    private function formatExtraServices(object $booking): void
    {
        $booking->extra_service_count = 0;
        $booking->extra_service_names = [];

        if (!empty($booking->extra_service)) {
            $extraServiceArray = json_decode($booking->extra_service, true);
            if (is_array($extraServiceArray)) {
                $booking->extra_service_formatted = $extraServiceArray;
                $booking->extra_service_count = count($extraServiceArray);
                $extraServiceIds = collect($extraServiceArray)->pluck('id')->toArray();
                $booking->extra_service_names = ExtraService::whereIn('id', $extraServiceIds)
                    ->pluck('name')
                    ->toArray();
            }
        }
    }

    private function formatInsurances(object $booking): void
    {
        $booking->insurance_count = 0;
        $booking->insurance_names = [];
        $booking->insurance_benefits_formatted = [];

        if (!empty($booking->insurance)) {
            $insuranceArray = json_decode($booking->insurance, true);
            if (is_array($insuranceArray)) {
                $booking->insurance_formatted = $insuranceArray;
                $booking->insurance_count = count($insuranceArray);
                $insuranceIds = collect($insuranceArray)->pluck('id')->toArray();
                $booking->insurance_names = Insurance::whereIn('id', $insuranceIds)
                    ->pluck('insurance_name')
                    ->toArray();
                $booking->insurance_benefits_formatted = InsuranceBenefit::whereIn('insurance_id', $insuranceIds)
                    ->pluck('benefit')
                    ->toArray();
            }
        }
    }

    private function formatMisc(object $booking): void
    {
        $booking->booking_status_text = Booking::getStatusLabel((int) ($booking->booking_status ?? 4));
        $booking->currency_symbol = getDefaultCurrencySymbol();

        if ($booking->delivery_type) {
            $booking->delivery_type = $booking->delivery_type === 'self_pickup' ? 'Self Pickup' : 'Delivery';
        }

        $fields = [
            'driver_price',
            'vehicle_price',
            'vehicle_total_price',
            'total_insurance_price',
            'total_extra_service_price',
            'final_price',
        ];

        foreach ($fields as $field) {
            $booking->{$field} = number_format((float) ($booking->{$field} ?? 0), 2, '.', '');
        }
    }
}
