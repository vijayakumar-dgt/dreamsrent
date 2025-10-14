<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\GeneralSetting;

class BookingCancellationService
{
    public function cancel(Booking $booking, Request $request, string $defaultCompanyName): array
    {
        $this->updateBookingStatus($booking, $request);

        $bookingDetail = BookingDetail::where('booking_id', $booking->id)->first();
        $historyData = [
            'bookings'        => $booking->toArray(),
            'booking_details' => $bookingDetail ? $bookingDetail->toArray() : [],
        ];

        $this->logHistory($booking, $historyData);
        $notifyData = $this->prepareNotificationData($booking, $defaultCompanyName);

        $this->sendNotifications($notifyData);

        return $notifyData;
    }

    private function updateBookingStatus(Booking $booking, Request $request): void
    {
        $booking->update([
            'cancel_reason'  => $request->cancel_reason,
            'cancel_by'      => Auth::guard('admin')->id() ?? $request->user_id,
            'cancel_date'    => now(),
            'booking_status' => 6,
        ]);
    }

    private function logHistory(Booking $booking, array $historyData): void
    {
        BookingHistory::create([
            'booking_id' => $booking->id,
            'action'     => 'cancel',
            'data'       => json_encode($historyData),
            'message'    => 'Booking cancelled',
        ]);
    }

    private function prepareNotificationData(Booking $booking, string $defaultCompanyName): array
    {
        $customer = \App\Models\User::find($booking->customer_id);
        $vehicle = VehicleInfo::find($booking->vehicle_id);
        $driver = Driver::find($booking->driver_id);
        $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? $defaultCompanyName;

        return [
            'user_name'       => $customer->name ?? '',
            'company_name'    => $companyName,
            'email'           => $customer->email ?? '',
            'phonenumber'     => $customer->phone_number ?? '',
            'vehicle_name'    => $vehicle->name ?? '',
            'driver_name'     => $driver?->driver_name ?? '',
            'reservation_id'  => $booking->reservation_id ?? '',
            'start_date'      => $booking->start_datetime ? formatDateTime($booking->start_datetime) : '',
            'end_date'        => $booking->end_datetime ? formatDateTime($booking->end_datetime) : '',
            'pickup_location' => $booking->pickupLocation?->name ?? '',
            'delivery_type'   => $booking->delivery_type ?? '',
            'rental_type'     => $booking->rental_type ?? '',
            'payment_type'    => $booking->payment_type ?? '',
            'payment_status'  => $booking->payment_status ?? '',
            'tototal_amount'  => $booking->final_price ?? '',
        ];
    }

    private function sendNotifications(array $notifyData): void
    {
        try {
            $appAdmin = \App\Models\User::where('user_type', 1)->first();

            if ($appAdmin?->email) {
                sendNotification($appAdmin->email, 'booking-cancelled-to-admin', $notifyData);
            }

            if (!empty($notifyData['email'])) {
                sendNotification($notifyData['email'], 'booking-cancelled-to-user', $notifyData);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
