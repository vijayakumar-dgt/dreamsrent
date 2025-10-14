<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;
use Modules\GeneralSetting\Models\GeneralSetting;

class BookingPersistenceService
{
    public function parseBookingDateTimes(
        string $startDate,
        string $startTime,
        string $endDate,
        string $endTime,
        string $displayFormat,
        string $dbFormat
    ): array {
        $startDateTime = \Illuminate\Support\Carbon::createFromFormat(
            $displayFormat,
            trim($startDate . ' ' . $startTime)
        );
        $endDateTime = \Illuminate\Support\Carbon::createFromFormat(
            $displayFormat,
            trim($endDate . ' ' . $endTime)
        );

        return [
            $startDateTime?->format($dbFormat),
            $endDateTime?->format($dbFormat)
        ];
    }

    public function prepareBookingData(array $payload, ?string $startDateTime, ?string $endDateTime): array
    {
        return [
            'vehicle_id'                => $payload['vehicle_id'] ?? null,
            'customer_id'               => $payload['customer_id'] ?? null,
            'booking_by'                => 'admin',
            'driver_id'                 => $payload['driver_id'] ?? null,
            'driver_price'              => $payload['driver_price'] ?? 0,
            'vehicle_price'             => $payload['vehicle_price'] ?? 0,
            'total_insurance_price'     => $payload['total_insurance_price'] ?? 0,
            'total_extra_service_price' => $payload['total_extra_service_price'] ?? 0,
            'final_price'               => $payload['final_price'] ?? 0,
            'extra_service'             => $payload['extra_service'] ?? null,
            'insurance'                 => $payload['insurance'] ?? null,
            'security_deposit'          => $payload['security_deposit'] ?? null,
            'start_datetime'            => $startDateTime,
            'end_datetime'              => $endDateTime,
            'pickup_location'           => $payload['pickup_location'] ?? null,
            'return_location'           => $payload['return_location'] ?? null,
            'booking_status'            => Booking::$booked,
            'booking_tariff'            => $payload['tariff'] ?? null,
            'driving_type'              => $payload['driving_type'] ?? null,
            'rental_type'               => $payload['vehicle_price_type'] ?? null,
            'no_of_passengers'          => $payload['no_of_passengers'] ?? null,
            'no_of_days'                => $payload['no_of_days'] ?? null,
            'vehicle_total_price'       => $payload['vehicle_total_price'] ?? null,
            'booking_date'              => now(),
            'payment_type'              => 'cod',
        ];
    }

    public function prepareBookingDetails(array $payload): array
    {
        $details = [
            'vehicle_price_type' => $payload['vehicle_price_type'] ?? null,
            'vehicle_season_id'  => $payload['vehicle_season_id'] ?? null,
            'vehicle_tariff_id'  => $payload['vehicle_tariff_id'] ?? null,
        ];

        if (!empty($payload['vehicle_tariff_id'])) {
            $tariff = VehicleTarrif::find($payload['vehicle_tariff_id']);
            if ($tariff) {
                $details = array_merge($details, [
                    'tariff_title'       => $tariff->tariff_title,
                    'tariff_price'       => $tariff->tariff_daily_price,
                    'tariff_from_days'   => $tariff->tariff_from_days,
                    'tariff_to_days'     => $tariff->tariff_to_days,
                    'tariff_base_km'     => $tariff->tariff_base_km,
                    'tariff_extra_price' => $tariff->tariff_extra_price,
                ]);
            }
        }

        if (!empty($payload['vehicle_season_id'])) {
            $season = VehicleSeason::find($payload['vehicle_season_id']);
            if ($season) {
                $details = array_merge($details, [
                    'seasonal_title'        => $season->seasonal_title,
                    'seasonal_start_date'   => $season->seasonal_start_date,
                    'seasonal_end_date'     => $season->seasonal_end_date,
                    'seasonal_daily_rate'   => $season->seasonal_daily_rate,
                    'seasonal_weekly_rate'  => $season->seasonal_weekly_rate,
                    'seasonal_monthly_rate' => $season->seasonal_monthly_rate,
                    'seasonal_late_fee'     => $season->seasonal_late_fee,
                ]);
            }
        }

        return $details;
    }

    public function createBooking(array $data, array $details): Booking
    {
        $data['created_by'] = Auth::guard('admin')->id();
        $booking = Booking::create($data);

        $bookingNumber = str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
        $prefix = GeneralSetting::where('key', 'reservation_prefix')->value('value') ?? 'RES';
        $booking->update(['reservation_id' => $prefix . $bookingNumber]);

        $details['booking_id'] = $booking->id;
        BookingDetail::create($details);

        $this->logBookingHistory($booking, $details, 'create', 'Reservation created');

        return $booking;
    }

    public function updateBooking(int $bookingId, array $data, array $details): Booking
    {
        $data['updated_by'] = Auth::guard('admin')->id();

        Booking::where('id', $bookingId)->update($data);
        BookingDetail::where('booking_id', $bookingId)->update($details);

        $booking = Booking::find($bookingId);
        if ($booking) {
            $this->logBookingHistory($booking, $details, 'update', 'Reservation updated');
        }

        return $booking;
    }

    public function logBookingHistory(Booking $booking, array $details, string $action, string $message): void
    {
        $historyData = [
            'bookings'        => $booking->toArray(),
            'booking_details' => $details,
        ];

        BookingHistory::create([
            'booking_id' => $booking->id,
            'action'     => $action,
            'data'       => json_encode($historyData),
            'message'    => $message,
        ]);
    }

    public function sendBookingNotifications(Booking $booking, string $defaultCompanyName): void
    {
        $customer = $booking->customer;
        $vehicle = $booking->vehicle;
        $driver = $booking->driver;
        $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? $defaultCompanyName;

        $notifyData = [
            'user_name'      => $customer->name ?? '',
            'company_name'   => $companyName,
            'email'          => $customer->email ?? '',
            'phonenumber'    => $customer->phone_number ?? '',
            'vehicle_name'   => $vehicle->name ?? '',
            'driver_name'    => $driver?->driver_name ?? '',
            'reservation_id' => $booking->reservation_id,
            'start_date'     => $booking->start_datetime ? formatDateTime($booking->start_datetime) : '',
            'end_date'       => $booking->end_datetime ? formatDateTime($booking->end_datetime) : '',
            'pickup_location'=> $booking->pickupLocation?->name ?? '',
            'delivery_type'  => $booking->delivery_type ?? '',
            'rental_type'    => $booking->rental_type ?? '',
            'payment_type'   => $booking->payment_type ?? '',
            'payment_status' => $booking->payment_status ?? '',
            'tototal_amount' => $booking->final_price ?? '',
        ];

        try {
            if (rentalNotificationEnabled()) {
                $admin = \App\Models\User::where('user_type', 1)->first();
                if ($admin?->email) {
                    sendNotification($admin->email, 'booking-confirmation-to-admin', $notifyData);
                }
            }

            if (userNotificationsEnabled() && $customer?->email) {
                sendNotification($customer->email, 'booking-confirmation-to-user', $notifyData);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
