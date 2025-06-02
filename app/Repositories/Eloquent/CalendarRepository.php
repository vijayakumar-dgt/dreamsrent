<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CalendarRepositoryInterface;
use App\Models\DrivingType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingUserInfo;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\PricingType;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;

class CalendarRepository implements CalendarRepositoryInterface
{
    public function index(): array
    {
        $cartypes = Cartype::get();
        $customerss = User::get();
        $Vehicles = VehicleInfo::get();
        $drivers = Driver::get();

        $locations = Location::where('status', 1)->get();
        $priceTypes = PricingType::where('type', 1)->get();
        $drivingTypes = DB::table('driving_types')->get();
        $customers = User::select(
            'users.id',
            DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name"),
        )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where(['users.user_type' => 3, 'users.status' => 1])
            ->get();

        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        $currency = null;

        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
        }

        $currencySymbol = $currency->symbol ?? "$";

        $data = [
            'cartypes' => $cartypes,
            'customerss' => $customerss,
            'Vehicles' => $Vehicles,
            'drivers' => $drivers,
            'locations' => $locations,
            'priceTypes' => $priceTypes,
            'drivingTypes' => $drivingTypes,
            'customers' => $customers,
            'currencySymbol' => $currencySymbol,
        ];
        return $data;
    }

    public function getCalenderBooking(Request $request): array
    {
        $status = $request->json('status');
        $vehicles = $request->json('vehicles');
        $customers = $request->json('customers');
        $drivers = $request->json('drivers');

        $query = Booking::with('userInfo');

        if (!empty($status)) {
            $query->where('booking_status', $status);
        }

        if (!empty($vehicles)) {
            $query->whereIn('vehicle_id', $vehicles);
        }

        if (!empty($customers)) {
            $query->whereIn('customer_id', $customers);
        }

        if (!empty($drivers)) {
            $query->whereIn('driver_id', $drivers);
        }
        $bookings = $query->get();

        $data = $bookings->map(function (Booking $booking): array {
            /** @var \Modules\Booking\Models\BookingUserInfo|null $userInfo */
            $userInfo = $booking->userInfo;

            $userName = "N/A";
            if ($booking->customer_id) {
                $user = User::select("name")->where('id', $booking->customer_id)->first();
                $userName = $user ? ucwords(strtolower($user->name)) : "N/A";
            }

            $fullName = $userInfo
                ? ucwords(strtolower("{$userInfo->first_name} {$userInfo->last_name}"))
                : $userName;

            return [
                'id' => $booking->id,
                'name' => $fullName,
                'booking_status' => $booking->booking_status,
                'booking_date' => $booking->booking_date,
                'start_datetime' => $booking->start_datetime,
                'end_datetime' => $booking->end_datetime,
                'created_at' => $booking->created_at,
            ];
        });

        return [
            'code' => 200,
            'message' => __('Booking List retrieved successfully.'),
            'data' => $data,
        ];
    }

    public function getBookingDetail(int $bookingId): array
    {
        /** @var \Modules\Booking\Models\Booking|null $booking */
        $booking = Booking::with('userInfo', 'vehicle')->find($bookingId);

        if (!$booking) {
            return [
                'code' => 404, 
                'message' => 'Booking not found'
            ];
        }

        /** @var \Modules\CarInfo\Models\VehicleInfo|null $vehicle */
        $vehicle = $booking->vehicle;
        $vehicleType = null;
        if ($vehicle) {
            $vehicle->vehicle_image = is_string($vehicle->vehicle_image)
                ? uploadedAsset($vehicle->vehicle_image ?? '')
                : uploadedAsset('', 'default');
            $vehicleTypeId = $vehicle->type_id ?? null;
            $vehicleType = Cartype::select('name')->where("id", $vehicleTypeId)->first();
        }

        $pickupLocation = Location::where('id', $booking->pickup_location)->value('name');

        $returnLocation = Location::where('id', $booking->return_location)->value('name');

        if (!empty($booking->driver_id)) {
            $driverDetails = Driver::select('driver_name', 'image', 'phone_number')
                ->where('id', $booking->driver_id)
                ->first();

            if ($driverDetails && $driverDetails->image) {
                $driverDetails->image = is_string($driverDetails->image)
                    ? uploadedAsset($driverDetails->image, 'profile')
                    : uploadedAsset('', 'profile');
            }
        } else {
            $bookingUser = BookingUserInfo::where('booking_id', $booking->id)->first();

            $driverDetails = (object) [
                'driver_name' => trim(($bookingUser->driver_first_name ?? '') . ' ' . ($bookingUser->driver_last_name ?? '')) ?: null,
                'phone_number' => $bookingUser->driver_mobile_number ?? null,
                'image' => uploadedAsset('', 'profile'),
            ];
        }

        $userInfo = User::where("id", $booking->customer_id)->first();

        if ($userInfo) {
            $userDetail = $userInfo->userDetail;

            $customerData = [
                'first_name' => $userDetail->first_name ?? '',
                'last_name' => $userDetail->last_name ?? '',
                'phone_number' => $userInfo->phone_number ?? '',
                'profile_image' => ($userDetail && $userDetail->profile_image)
                    ? uploadedAsset($userDetail->profile_image, 'profile')
                    : uploadedAsset('', 'profile'),
            ];
        } else {
            $customerData = null;
        }
        $drivingTypeId = $booking->driving_type ?? null;
        $booking->driver_type_info = null;
        if ($drivingTypeId) {
            $booking->driver_type_info = DrivingType::where('id', $drivingTypeId)->first();
        }
        $booking->delivery_type = $booking->delivery_type ? ucfirst(str_replace('_', ' ', $booking->delivery_type)) : 'N/A';

        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        $currency = null;

        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
        }

        $currencySymbol = $currency->symbol ?? "$";
        return [
            'code' => 200,
            'booking' => $booking,
            'vehicleType' => $vehicleType,
            'pickupLocation' => $pickupLocation,
            'returnLocation' => $returnLocation,
            'driverDetails' => $driverDetails,
            'customerDetails' => $customerData,
            'currency' => $currencySymbol,
        ];
    }
}