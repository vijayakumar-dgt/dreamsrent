<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\GeneralSetting;

class QuotationBookingPersistence
{
    public function createBooking(array $data, array $details): Booking
    {
        $data['created_by'] = Auth::guard('admin')->id();
        $booking = Booking::create($data);
        $bookingNumber = str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
        $bookingPrefix = GeneralSetting::select('value')->where('key', 'reservation_prefix')->first();
        $bookingPrefix = $bookingPrefix->value ?? 'RES';
        $booking->update(['reservation_id' => $bookingPrefix . $bookingNumber]);
        $details['booking_id'] = $booking->id;

        $bookingDetail = BookingDetail::create($details);

        $historyData = [
            'bookings'        => $booking->toArray(),
            'booking_details' => $bookingDetail->toArray(),
        ];

        BookingHistory::create([
            'booking_id' => $booking->id,
            'action'     => 'create',
            'data'       => json_encode($historyData),
            'message'    => 'Quotations created',
        ]);

        $this->sendNotification($booking);

        return $booking;
    }

    public function updateBooking(int|string $bookingId, array $data, array $details): void
    {
        $data['updated_by'] = Auth::guard('admin')->id();

        Booking::where('id', $bookingId)->update($data);
        BookingDetail::where('booking_id', $bookingId)->update($details);

        $booking = Booking::find($bookingId);
        $bookingDetail = BookingDetail::where('booking_id', $bookingId)->first();

        $historyData = [
            'bookings'        => $booking instanceof Booking ? $booking->toArray() : [],
            'booking_details' => $bookingDetail instanceof BookingDetail ? $bookingDetail->toArray() : [],
        ];

        if ($booking instanceof Booking) {
            BookingHistory::create([
                'booking_id' => $booking->id,
                'action'     => 'update',
                'data'       => json_encode($historyData),
                'message'    => 'Quotations updated',
            ]);
        }
    }

    private function sendNotification(Booking $booking): void
    {
        $customer = User::where('id', $booking->customer_id)->first();
        $vehicle = VehicleInfo::where('id', $booking->vehicle_id)->first();
        $driver = Driver::find($booking->driver_id);
        $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';

        $notifyData = [
            'user_name'       => $customer->name ?? '',
            'company_name'    => $companyName,
            'email'           => $customer->email ?? '',
            'phonenumber'     => $customer->phone_number ?? '',
            'vehicle_name'    => $vehicle->name ?? '',
            'driver_name'     => $driver ? $driver->driver_name : '',
            'reservation_id'  => $booking->reservation_id ?? '',
            'start_date'      => $booking->start_datetime ? formatDateTime($booking->start_datetime) : '',
            'end_date'        => $booking->end_datetime ? formatDateTime($booking->end_datetime) : '',
            'pickup_location' => $booking->pickupLocation ? $booking->pickupLocation->name : '',
            'delivery_type'   => $booking->delivery_type ?? '',
            'rental_type'     => $booking->rental_type ?? '',
            'payment_type'    => $booking->payment_type ?? '',
            'payment_status'  => $booking->payment_status ?? '',
            'tototal_amount'  => $booking->final_price ?? '',
        ];

        try {
            if (rentalNotificationEnabled()) {
                $appAdmin = User::where('user_type', 1)->first();
                if ($appAdmin !== null) {
                    sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                }

                if ($customer !== null) {
                    sendNotification($customer->email, 'booking-confirmation-to-user', $notifyData);
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
