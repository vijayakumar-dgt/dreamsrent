<?php

namespace Modules\Booking\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingHistory;
use Modules\Booking\Models\BookingUserInfo;
use Modules\GeneralSetting\Models\GeneralSetting;

class BookingBuilder
{
    public function buildBookingData(Request $request, User $authUser, array $formattedData): array
    {
        return [
            "vehicle_id"                => $request->input('vehicle_id'),
            "booking_by"                => "user",
            "booking_date"              => $formattedData['formattedBookingDate'],
            "start_datetime"            => $formattedData['startDatetime'],
            "end_datetime"              => $formattedData['endDatetime'],
            "pickup_location"           => $formattedData['pickup_location_id'],
            "return_location"           => $formattedData['return_location_id'],
            "delivery_location"         => $formattedData['pickup_location'] ?? null,
            "delivery_return_location"  => $formattedData['return_location'] ?? null,
            "delivery_type"             => $request->rent_type,
            "rental_type"               => $request->price_type_value,
            "security_deposit"          => $request->input('security_deposit') ?? null,
            "booking_tariff"            => $request->input('booking_tariff') ?? null,
            "driving_type"              => $request->input('driving_type') ?? null,
            "no_of_passengers"          => $request->input('no_person') ?? null,
            "no_of_days"                => $formattedData['noOfDays'],
            "customer_id"               => $authUser->id ?? 0,
            "driver_id"                 => $request->input('driver_id') ?? 0,
            "driver_price"              => $request->input('driver_price_total'),
            "extra_service"             => $request->input('extra_services'),
            "insurance"                 => $request->input('insurance'),
            "total_insurance_price"     => $request->input('insurance_price_total'),
            "total_extra_service_price" => $request->input('extra_price_total'),
            "vehicle_price"             => $request->input('vehicle_price'),
            "vehicle_total_price"       => $request->input('vehicle_price_total'),
            "final_price"               => $request->input('total_price'),
            "cancel_date"               => $request->input('cancel_date') ?? null,
            "cancel_by"                 => $request->input('cancel_by') ?? null,
            "cancel_reason"             => $request->input('cancel_reason') ?? null,
            "created_by"                => $request->input('created_by') ?? null,
            "updated_by"                => $request->input('updated_by') ?? null,
            "tax_val"                   => $request->tax_val ?? null,
        ];
    }

    public function buildUserInfoData(Request $request, Booking $booking): array
    {
        return [
            'booking_id'           => $booking->id,
            'driver_first_name'    => $request->driver_first_name,
            'driver_last_name'     => $request->driver_last_name,
            'driver_age'           => $request->driver_age,
            'driver_mobile_number' => $request->driver_mobile_number,
            'driver_licence'       => $request->driver_licence,
            'driver_check'         => $request->has('driver_check') ? 1 : 0,
            'first_name'           => $request->first_name,
            'last_name'            => $request->last_name,
            'no_person'            => $request->no_person ?? 0,
            'company'              => $request->company,
            'address'              => $request->address,
            'country_id'           => $request->country_id,
            'state_id'             => $request->state_id,
            'city_id'              => $request->city_id,
            'pincode'              => $request->pincode,
            'email'                => $request->email,
            'phone_number'         => $request->phone_number,
            'add_info'             => $request->add_info,
            'terms'                => $request->has('terms') ? 1 : 0,
        ];
    }

    public function createBookingWithInfo(array $bookingData, array $userInfoData): Booking
    {
        $booking = Booking::create($bookingData);

        $dataForHistory = [
            'bookings'        => $booking->toArray(),
            'booking_details' => []
        ];

        BookingHistory::create([
            'booking_id' => $booking->id,
            'data'       => json_encode($dataForHistory),
            'action'     => 'create',
            'message'    => __('web.home.booking_created'),
        ]);

        $reservationId = $this->getReservationId($booking->id);
        $booking->update(['reservation_id' => $reservationId]);

        $userInfoData['booking_id'] = $booking->id;
        BookingUserInfo::create($userInfoData);

        return $booking;
    }

    private function getReservationId(?int $bookingId): string
    {
        $bookingId = str_pad((string) $bookingId, 4, '0', STR_PAD_LEFT);
        $bookingPrefix = GeneralSetting::where('key', 'reservation_prefix')->value('value');
        $bookingPrefix = $bookingPrefix ?? 'RES';

        return $bookingPrefix . $bookingId;
    }
}
