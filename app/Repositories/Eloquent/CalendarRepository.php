<?php

namespace App\Repositories\Eloquent;

use App\Models\DrivingType;
use App\Models\User;
use App\Repositories\Contracts\CalendarRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $vehicles = VehicleInfo::get();
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
        return [
            'cartypes'       => $cartypes,
            'customerss'     => $customerss,
            'Vehicles'       => $vehicles,
            'drivers'        => $drivers,
            'locations'      => $locations,
            'priceTypes'     => $priceTypes,
            'drivingTypes'   => $drivingTypes,
            'customers'      => $customers,
            'currencySymbol' => $currencySymbol,
        ];
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
                'id'             => $booking->id,
                'name'           => $fullName,
                'booking_status' => $booking->booking_status,
                'booking_date'   => $booking->booking_date,
                'start_datetime' => $booking->start_datetime,
                'end_datetime'   => $booking->end_datetime,
                'created_at'     => $booking->created_at,
            ];
        });

        return [
            'code'    => 200,
            'message' => __('Booking List retrieved successfully.'),
            'data'    => $data,
        ];
    }

    public function getBookingDetail(int $bookingId): array
    {
        /** @var \Modules\Booking\Models\Booking|null $booking */
        $booking = Booking::with('userInfo', 'vehicle')->find($bookingId);

        if (!$booking) {
            return [
                'code'    => 404,
                'message' => 'Booking not found',
            ];
        }

        $vehicleType = $this->processVehicle($booking);
        $driverDetails = $this->processDriver($booking);
        $customerData = $this->processCustomer($booking->customer_id);
        $pickupLocation = $this->getLocationName($booking->pickup_location);
        $returnLocation = $this->getLocationName($booking->return_location);

        $booking->driver_type_info = $booking->driving_type ? DrivingType::find($booking->driving_type) : null;

        $booking->delivery_type = $booking->delivery_type
            ? ucfirst(str_replace('_', ' ', $booking->delivery_type))
            : 'N/A';

        $currencySymbol = getDefaultCurrencySymbol();

        return [
            'code'            => 200,
            'booking'         => $booking,
            'vehicleType'     => $vehicleType,
            'pickupLocation'  => $pickupLocation,
            'returnLocation'  => $returnLocation,
            'driverDetails'   => $driverDetails,
            'customerDetails' => $customerData,
            'currency'        => $currencySymbol,
        ];
    }

    /**
     * Process vehicle and return vehicle type
     */
    private function processVehicle($booking)
    {
        $vehicle = $booking->vehicle;

        if (!$vehicle) {
            return null;
        }

        $vehicleImagePath = $vehicle->vehicle_image ?? '';
        $filename = basename($vehicleImagePath);
        $newPath = 'vehicles/images/small/' . $filename;
        $file = public_path('storage/' . $newPath);

        $vehicle->vehicle_image = uploadedAsset(file_exists($file) ? $newPath : $vehicleImagePath);

        return Cartype::select('name')->where('id', $vehicle->type_id ?? 0)->first();
    }

    /**
     * Process driver info
     */
    private function processDriver($booking)
    {
        if (!empty($booking->driver_id)) {
            $driverDetails = Driver::select('driver_name', 'image', 'phone_number')
                ->where('id', $booking->driver_id)
                ->first();

            if ($driverDetails) {
                $driverDetails->image = uploadedAsset(is_string($driverDetails->image) ? $driverDetails->image : '', 'profile');
            }
        } else {
            $bookingUser = BookingUserInfo::where('booking_id', $booking->id)->first();
            $driverDetails = (object) [
                'driver_name'  => trim(($bookingUser->driver_first_name ?? '') . ' ' . ($bookingUser->driver_last_name ?? '')) ?: null,
                'phone_number' => $bookingUser->driver_mobile_number ?? null,
                'image'        => uploadedAsset('', 'profile'),
            ];
        }

        return $driverDetails;
    }

    /**
     * Process customer info
     */
    private function processCustomer($customerId)
    {
        $userInfo = User::find($customerId);
        if (!$userInfo) {
            return null;
        }

        $userDetail = $userInfo->userDetail;

        return [
            'first_name'    => $userDetail->first_name ?? '',
            'last_name'     => $userDetail->last_name ?? '',
            'phone_number'  => $userInfo->phone_number ?? '',
            'profile_image' => uploadedAsset($userDetail->profile_image ?? '', 'profile'),
        ];
    }

    /**
     * Fetch location name by ID
     */
    private function getLocationName($locationId)
    {
        return Location::where('id', $locationId)->value('name');
    }
}
