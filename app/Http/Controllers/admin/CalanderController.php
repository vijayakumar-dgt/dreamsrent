<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DrivingType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingUserInfo;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\PricingType;
use Modules\CarInfo\Models\VehicleInfo;

class CalanderController extends Controller
{
    public function index()
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

        return view('admin.calender.index', compact("Vehicles", "customerss", "drivers", "cartypes", 'locations', 'priceTypes', 'drivingTypes', 'customers'));
    }

    public function getCalenderBooking(Request $request)
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

        $data = $bookings->map(function ($booking) {
            $userInfo = $booking->userInfo;

            $userName = "N/A";
            if ($booking && $booking->customer_id) {
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

        return response()->json([
            'code' => 200,
            'message' => __('Booking List retrieved successfully.'),
            'data' => $data,
        ]);
    }


    public function getBookingDetail(Request $request)
    {
        $bookingId = $request->get('booking_id');

        $booking = Booking::with('userInfo', 'vehicle')->find($bookingId);

        if (!$booking) {
            return response()->json(['code' => 404, 'message' => 'Booking not found']);
        }

        $booking->vehicle->vehicle_image = asset('/storage/' . $booking->vehicle->vehicle_image);

        $vehicleType = Cartype::select('name')->where("id", $booking->vehicle->type_id)->first();

        $pickupLocation = $booking->delivery_location
            ? $booking->delivery_location
            : Location::where('id', $booking->pickup_location)->value('name');

        $returnLocation = $booking->delivery_return_location
            ? $booking->delivery_return_location
            : Location::where('id', $booking->return_location)->value('name');

        if (!empty($booking->driver_id) && $booking->driver_id != 0) {
            $driverDetails = Driver::select('driver_name', 'image', 'phone_number')
                ->where('id', $booking->driver_id)
                ->first();

            if ($driverDetails && $driverDetails->image) {
                $driverDetails->image = asset('/storage/' . $driverDetails->image);
            }
        } else {
            $bookingUser = BookingUserInfo::where('booking_id', $booking->id)->first();

            $driverDetails = (object) [
                'driver_name'    => $bookingUser->driver_first_name ?? null,
                'phone_number'   => $bookingUser->driver_mobile_number ?? null,
                'image'          => null,
            ];
        }

        $userInfo = User::where("id", $booking->customer_id)->first();

        if ($userInfo) {
            $userDetail = $userInfo->userDetail; // Assuming hasOne relationship
        
            $customerData = [
                'first_name' => $userDetail->first_name ?? '',
                'last_name' => $userDetail->last_name ?? '',
                'phone_number' => $userInfo->phone_number ?? '',
                'profile_image' => $userDetail->profile_image 
                    ? url('storage/' . $userDetail->profile_image) 
                    : url('assets/img/default-avatar.jpg'),
            ];
        } else {
            $customerData = null;
        }
        $booking->driver_type_info = DrivingType::where('id', $booking->driving_type)->first();

        $booking->delivery_type = $booking->delivery_type ?? 'N/A';

        return response()->json(['code' => 200, 'booking' => $booking, 'vehicleType' => $vehicleType, 'pickupLocation' => $pickupLocation, "returnLocation" => $returnLocation, "driverDetails" => $driverDetails, 'customerDetails' => $customerData]);
    }
}
