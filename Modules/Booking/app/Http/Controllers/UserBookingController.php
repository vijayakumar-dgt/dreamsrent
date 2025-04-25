<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Models\WalletHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleInsurance;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Modules\Booking\Models\BookingHistory;
use Modules\Booking\Models\BookingUserInfo;
use Modules\CarInfo\Models\ExtraService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Modules\Communication\Http\Controllers\EmailController;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\SubTax;
use Modules\GeneralSetting\Models\TaxGroup;
use Modules\GeneralSetting\Models\TaxRate;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;

class UserBookingController extends Controller
{
    private $provider;
    public function __construct()
    {
        $this->provider = new PayPalClient;
        $this->provider->getAccessToken();
    }

    public function redirectToBooking(Request $request)
    {
        if (session()->has('intended_booking')) {
            $booking = session('intended_booking');
            session()->forget('intended_booking');
            $slug = $booking['slug'];
            $data = $booking['data'];
            return view('frontend.redirect-to-booking', compact('slug', 'data'));
        }
        return redirect()->route('home');
    }
    public function index(Request $request, $slug)
    {
        if (!Auth::guard('web')->check()) {
            session(['intended_url' => url()->current()]);
            session([
                'intended_booking' => [
                    'slug' => $request->slug,
                    'data' => $request->except('_token')
                ]
            ]);
            return redirect()->route('user-login');
        }
        $vehicle = VehicleInfo::select('id', 'name', 'vehicle_image', 'main_location_id', 'other_location_id', 'vehicle_price', "passenger_capacity")->where('slug', $slug)->first();
        $vehicleImageUrl = $vehicle ? asset('/storage/' . $vehicle->vehicle_image) : null;

        $vehicleId = $vehicle->id;

        $prices = json_decode($vehicle->vehicle_price, true)[0] ?? [];

        $filteredPrices = array_filter($prices, function ($price) {
            return $price > 0;
        });

        $mainLocation = Location::select('name', 'address')->where('id', $vehicle->main_location_id)->first();


        $allLocation = collect();

        // Get main location
        if ($vehicle->main_location_id) {
            $mainLocations = Location::select('id', 'name', 'address')
                ->where('id', $vehicle->main_location_id)
                ->first();

            if ($mainLocations) {
                $allLocation->push($mainLocations);
            }
        }

        // Get other locations and remove duplicates
        if (!empty($vehicle->other_location_id)) {
            $otherIds = json_decode($vehicle->other_location_id, true);

            if (is_array($otherIds)) {
                // Remove main_location_id if present in other_location_id
                $filteredOtherIds = array_filter($otherIds, function ($id) use ($vehicle) {
                    return $id != $vehicle->main_location_id;
                });

                if (!empty($filteredOtherIds)) {
                    $otherLocations = Location::select('id', 'name', 'address')
                        ->whereIn('id', $filteredOtherIds)
                        ->get();

                    $allLocation = $allLocation->merge($otherLocations);
                }
            }
        }
        $dlocation = Location::select('id', 'name', 'address')->where('id', $request->delivery_location)->first();
        $plocation = Location::select('id', 'name', 'address')->where('id', $request->pickup_location)->first();
        $rlocation = Location::select('id', 'name', 'address')->where('id', $request->delivery_return_location)->first();
        $prlocation = Location::select('id', 'name', 'address')->where('id', $request->pickup_return_location)->first();

        $extraServices = VehicleExtraService::with(['extraService:id,name,icon,description'])
            ->select("extra_service_id", "value", "price")
            ->where('vehicle_id', $vehicleId)
            ->get()
            ->map(function ($service) {
                if (!empty($service->extraService->icon)) {
                    $service->extraService->icon = asset('storage/' . $service->extraService->icon);
                }
                return $service;
            });

        $extraServiceCount = $extraServices->count();

        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        $currency = null;

        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
        }

        $currencySymbol = $currency->symbol ?? "$";


        $vehicleInsurance = VehicleInsurance::select(
            'vehicle_insurances.insurances_id',
            'vehicle_insurances.value',
            'vehicle_insurances.price',
            'insurances.insurance_name',
            'insurances.price as insurance_price',
            'insurances.price_type_id',
            'insurances.status'
        )
            ->join('insurances', 'vehicle_insurances.insurances_id', '=', 'insurances.id')
            ->with(['insuranceBenefits' => function ($query) {
                $query->select('insurance_id', 'benefit');
            }])
            ->where('vehicle_insurances.vehicle_id', $vehicleId)
            ->get();

        $vehicleInsurance->transform(function ($insurance) {
            $insurance->benefits_count = $insurance->insuranceBenefits->count();
            $insurance->first_benefit = $insurance->insuranceBenefits->first()?->benefit ?? 'No benefits available';
            return $insurance;
        });

        $countries = Country::get();

        $driverInfo = Driver::select("id", "driver_name")->where("assigned_cars", $vehicleId)->first();
        $driverInfo_ride = 35;
        $driverInfo_price = 0;

        $finalRate = (float) $request->final_price_rate;

        // Get all tax groups with their tax rates using Eloquent relationship
        $taxGroups = TaxGroup::with(['taxRates'])->get();

        // Prepare an array to store calculated tax details
        $calculatedTaxes = [];

        foreach ($taxGroups as $group) {
            $groupTaxAmount = 0;

            foreach ($group->taxRates as $rate) {
                $taxAmount = ($rate->tax_rate / 100) * $finalRate;
                $groupTaxAmount += $taxAmount;

                $calculatedTaxes[] = [
                    'group_name' => $group->tax_name,
                    'rate_name' => $rate->tax_name,
                    'rate_percent' => $rate->tax_rate,
                    'amount' => $taxAmount,
                ];
            }
        }
        $totalTax = array_sum(array_column($calculatedTaxes, 'amount'));
        $grandTotal = $finalRate + $totalTax;
        $user = Auth::guard('web')->user();
        $seo_title = "User Booking";

        return view('booking::user_booking.index', compact("slug", "user", "vehicleId", "vehicle", "vehicleImageUrl", "mainLocation", "filteredPrices", "extraServices", "extraServiceCount", "vehicleInsurance", "countries", "driverInfo", "driverInfo_ride", "driverInfo_price", "allLocation", "dlocation", "rlocation", 'finalRate', 'calculatedTaxes', 'totalTax', 'grandTotal', "seo_title", "plocation", "prlocation", "currencySymbol"))
            ->with($request->all());
    }

    public function getStates($country_id)
    {
        $states = State::where('country_id', $country_id)->get(['id', 'name']);
        return response()->json($states);
    }

    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->get(['id', 'name']);
        return response()->json($cities);
    }

    public function checkBooking(Request $request)
    {
        $pickupDatetime = $request->input('start_datetime');
        $returnDatetime = $request->input('end_datetime');
        $vehicleId = $request->input('vehicle_id');

        if (empty($pickupDatetime) || empty($returnDatetime) || empty($vehicleId)) {
            return response()->json([
                'status' => 'error',
                'message' => __('web.home.booking_required_fields')
            ], 422);
        }

        // Check for overlapping bookings on the same vehicle
        $isUnavailable = Booking::where('vehicle_id', $vehicleId)
            ->where(function ($query) use ($pickupDatetime, $returnDatetime) {
                $query->where('start_datetime', '<', $returnDatetime)
                    ->where('end_datetime', '>', $pickupDatetime);
            })
            ->exists();

        if ($isUnavailable) {
            return response()->json([
                'status' => 'error',
                'message' => __('web.home.vehicle_already_booked_for_selected_time')
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('web.home.vehicle_available')
        ]);
    }


    public function paymentSuccess($transaction_id)
    {
        $booking = Booking::where('transaction_id', $transaction_id)->first();

        $vehicleId = $booking->vehicle_id;

        if ($booking) {
            $extraServices = json_decode($booking->extra_service, true);
            $extraServiceIds = collect($extraServices)->pluck('id')->toArray();
        }

        $vehicle = VehicleInfo::select('id', 'name', 'vehicle_image', 'main_location_id', 'vehicle_price')->where('id', $vehicleId)->first();
        $vehicleImageUrl = $vehicle ? asset('/storage/' . $vehicle->vehicle_image) : null;

        $mainLocation = Location::select('name', 'address')->where('id', $vehicle->main_location_id)->first();
        $dLocation = Location::select('name', 'address')->where('id', $booking->pickup_location)->first();
        $rLocation = Location::select('name', 'address')->where('id', $booking->return_location)->first();
        $vehicleExtraServices = ExtraService::select('id', 'name')
            ->whereIn('id', $extraServiceIds)
            ->get();

        $vehicleExtraServicesWithPrice = $vehicleExtraServices->map(function ($service) use ($vehicleId) {
            $serviceData = VehicleExtraService::where('extra_service_id', $service->id)
                ->where('vehicle_id', $vehicleId)
                ->first(['price', 'value']);

            $service->price = $serviceData->price ?? 0; // Default to 0 if no price found
            $service->value = $serviceData->value ?? null; // Include value

            return $service;
        });

        $vehicleInsurance = VehicleInsurance::select(
            'vehicle_insurances.insurances_id',
            'vehicle_insurances.value',
            'vehicle_insurances.price',
            'insurances.insurance_name',
            'insurances.price as insurance_price',
            'insurances.price_type_id',
            'insurances.status'
        )
            ->join('insurances', 'vehicle_insurances.insurances_id', '=', 'insurances.id')
            ->with(['insuranceBenefits' => function ($query) {
                $query->select('insurance_id', 'benefit'); // Only select necessary columns
            }])
            ->where('vehicle_insurances.vehicle_id', $vehicleId)
            ->get();

        $driverInfo = Driver::select("id", "driver_name")->where("assigned_cars", $vehicleId)->first();
        $driverInfo_ride = 35;
        $driverInfo_price = 100;

        $bookingInfo = BookingUserInfo::where("booking_id", $booking->id)->first();

        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        $currency = null;

        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
        }

        $currencySymbol = $currency->symbol ?? "$";


        return view("booking::user_booking.success_page", compact("transaction_id", "booking", "vehicleId", "vehicle", "vehicleImageUrl", "dLocation", "rLocation", "mainLocation", "vehicleExtraServicesWithPrice", "vehicleInsurance", "driverInfo", "driverInfo_ride", "driverInfo_price", "bookingInfo", "currencySymbol"));
    }


    public function userPayments(Request $request)
    {
        $authUser = current_user();

        $formattedBookingDate = Carbon::now()->format('Y-m-d H:i:s');

        $startDatetime = Carbon::createFromFormat('d-m-Y H:i', $request->input('pickup_date') . ' ' . $request->input('pickup_time'))
            ->format('Y-m-d H:i:s');

        $endDatetime = Carbon::createFromFormat('d-m-Y H:i', $request->input('return_date') . ' ' . $request->input('return_time'))
            ->format('Y-m-d H:i:s');

        $noOfDays = Carbon::parse($startDatetime)->diffInDays(Carbon::parse($endDatetime)) + 1;

        if ($request->rent_type == "delivery") {
            $pickup_location_id = $request->delivery_location;
            $return_location_id = $request->delivery_return_location;
        } else if ($request->rent_type == "self_pickup") {
            $pickup_location_id = $request->pickup_location;
            $return_location_id = $request->pickup_return_location;
        }

        if ($request->payment_type == "cod") {

            $generateID = 'COD' . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $data = [
                "vehicle_id" => $request->input('vehicle_id'),
                "booking_status" => 4,
                "booking_by" => "user",
                "booking_date" => $formattedBookingDate,
                "start_datetime" => $startDatetime,
                "end_datetime" => $endDatetime,
                "pickup_location" => $pickup_location_id ?? null,
                "return_location" => $return_location_id ?? null,
                "delivery_location" => $pickup_location ?? null,
                "delivery_return_location" => $return_location ?? null,
                "delivery_type" => $request->rent_type,
                "rental_type" => $request->price_type_value,
                "security_deposit" => $request->input('security_deposit') ?? null,
                "booking_tariff" => $request->input('booking_tariff')  ?? null,
                "driving_type" => $request->input('driving_type') ?? null,
                "no_of_passengers" => $request->input('no_person') ?? null,
                "no_of_days" => $noOfDays,
                "customer_id" => $authUser->id ?? 0,
                "driver_id" => $request->input('driver_id') ?? 0,
                "driver_price" => $request->input('driver_price_total'),
                "extra_service" => $request->input('extra_services'),
                "insurance" => $request->input('insurance'),
                "total_insurance_price" => $request->input('insurance_price_total'),
                "total_extra_service_price" => $request->input('extra_price_total'),
                "vehicle_price" => $request->input('vehicle_price'),
                "vehicle_total_price" => $request->input('vehicle_price_total'),
                "final_price" => $request->input('total_price'),
                "cancel_date" => $request->input('cancel_date') ?? null,
                "cancel_by" => $request->input('cancel_by') ?? null,
                "cancel_reason" => $request->input('cancel_reason') ?? null,
                "created_by" => $request->input('created_by') ?? null,
                "updated_by" => $request->input('updated_by') ?? null,
                "transaction_id" => $generateID,
                "payment_status" =>  1,
                "payment_type" =>  "cod",
                "tax_val" =>  $request->tax_val ?? null,
            ];

            $booking = Booking::create($data);

            $dataForHistory = [
                'bookings' => $booking->toArray(),
                'booking_details' => [] // keep empty for now
            ];

            BookingHistory::create([
                'booking_id' => $booking->id,
                'data' => json_encode($dataForHistory),
                'action' => 'create',
                'message' => __('web.home.booking_created'),
            ]);

            $reservationId = 'RES-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

            $booking->update(['reservation_id' => $reservationId]);

            $addData = [
                'booking_id' => $booking->id,
                'driver_first_name' => $request->driver_first_name,
                'driver_last_name' => $request->driver_last_name,
                'driver_age' => $request->driver_age,
                'driver_mobile_number' => $request->driver_mobile_number,
                'driver_licence' => $request->driver_licence,
                'driver_check' => $request->has('driver_check') ?? 1,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'no_person' => $request->no_person ?? 0,
                'company' => $request->company,
                'address' => $request->address,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'pincode' => $request->pincode,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'add_info' => $request->add_info,
                'terms' => $request->has('terms') ?? 1,
            ];

            $bookingInfo = BookingUserInfo::create($addData);

            //send notification to admin
            $authUser = Auth::guard('web')->user();
            $vehicle = VehicleInfo::where('id', $request->vehicle_id)->first();
            $driver  = Driver::find($booking->driver_id);
            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $notifyData = [
                'user_name' => $authUser->name ?? '',
                'company_name' => $companyName,
                'email'     => $authUser->email ?? '',
                'phonenumber' => $authUser->phone_number ?? '',
                'vehicle_name' => $vehicle->name ?? "",
                'driver_name'  => $driver ? $driver->driver_name : "",
                'reservation_id' => $booking->reservation_id ?? "",
                'start_date'     => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                'end_date'       => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
                'pickup_location' => $booking->pickupLocation ? $booking->pickupLocation->name : "",
                'delivery_type'   => $booking->delivery_type ?? "",
                'rental_type'     => $booking->rental_type ?? "",
                'payment_type'    => $booking->payment_type ?? "",
                'payment_status'  => $booking->payment_status ?? "",
                'tototal_amount'  => $booking->final_price ?? ""
            ];
            if (rentalNotificationEnabled()) {
                $appAdmin = User::where('user_type', 1)->first();
                sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);

                sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
            }

            if ($booking) {
                return response()->json([
                    'code' => 200,
                    'message' => __('web.home.booking_successfully_created'),
                    'email' => $request->email,
                    'cod' => $booking->transaction_id,
                    'redirect_url' => route('payment.success.page', ['transaction_id' => $booking->transaction_id])
                ], 200);
            } else {
                return response()->json(['error' => 'Failed to create booking'], 500);
            }
        }

        if ($request->payment_type == "paypal") {
            $order['intent'] = 'CAPTURE';

            $currency_details = "USD"; // Fix currency

            $purchase_units = [];

            $unit = [
                'items' => [
                    [
                        'name' => 'Rental System',
                        'quantity' => 1,
                        'unit_amount' => [
                            'currency_code' => $currency_details,
                            'value' => $request->total_price,
                        ]
                    ],
                ],
                'amount' => [
                    'currency_code' => $currency_details,
                    'value' => $request->total_price,
                    'breakdown' => [
                        'item_total' => [
                            'currency_code' => $currency_details,
                            'value' => $request->total_price,
                        ],
                    ]
                ]
            ];

            $purchase_units[] = $unit;

            $order['purchase_units'] = $purchase_units;

            $order['application_context'] = [
                'return_url' => url('paypal-payment-success'),
                'cancel_url' => url('payment-failed')
            ];

            $response = $this->provider->createOrder($order);

            if (!$response || !isset($response['id'])) {
                return response()->json([
                    'code' => 500,
                    'message' => __('web.home.paypal_order_failded'),
                ]);
            }

            $data = [
                "vehicle_id" => $request->input('vehicle_id'),
                "booking_status" => 4,
                "booking_by" => "user",
                "booking_date" => $formattedBookingDate,
                "start_datetime" => $startDatetime,
                "end_datetime" => $endDatetime,
                "pickup_location" => $pickup_location_id ?? null,
                "return_location" => $return_location_id ?? null,
                "delivery_location" => $pickup_location ?? null,
                "delivery_return_location" => $return_location ?? null,
                "delivery_type" => $request->rent_type,
                "rental_type" => $request->price_type_value,
                "security_deposit" => $request->input('security_deposit') ?? null,
                "booking_tariff" => $request->input('booking_tariff') ?? null,
                "driving_type" => $request->input('driving_type') ?? null,
                "no_of_passengers" => $request->input('no_person') ?? null,
                "no_of_days" => $noOfDays,
                "customer_id" => $authUser->id ?? 0,
                "driver_id" => $request->input('driver_id') ?? 0,
                "driver_price" => $request->input('driver_price_total'),
                "extra_service" => $request->input('extra_services'),
                "insurance" => $request->input('insurance'),
                "total_insurance_price" => $request->input('insurance_price_total'),
                "total_extra_service_price" => $request->input('extra_price_total'),
                "vehicle_price" => $request->input('vehicle_price'),
                "vehicle_total_price" => $request->input('vehicle_price_total'),
                "final_price" => $request->input('total_price'),
                "cancel_date" => $request->input('cancel_date') ?? null,
                "cancel_by" => $request->input('cancel_by') ?? null,
                "cancel_reason" => $request->input('cancel_reason') ?? null,
                "created_by" => $request->input('created_by') ?? null,
                "updated_by" => $request->input('updated_by') ?? null,
                "transaction_id" =>  $response['id'],
                "payment_status" =>  1,
                "payment_type" =>  "paypal",
                "tax_val" =>  $request->tax_val ?? null,
            ];

            $booking = Booking::create($data);

            $reservationId = 'RES-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
            $booking->update(['reservation_id' => $reservationId]);

            $addData = [
                'booking_id' => $booking->id,
                'driver_first_name' => $request->driver_first_name,
                'driver_last_name' => $request->driver_last_name,
                'driver_age' => $request->driver_age,
                'driver_mobile_number' => $request->driver_mobile_number,
                'driver_licence' => $request->driver_licence,
                'driver_check' => $request->has('driver_check') ?? 1,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'no_person' => $request->no_person ?? 0,
                'company' => $request->company,
                'address' => $request->address,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'pincode' => $request->pincode,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'add_info' => $request->add_info,
                'terms' => $request->has('terms') ?? 1,
            ];

            $bookingInfo = BookingUserInfo::create($addData);

            $dataForHistory = [
                'bookings' => $booking->toArray(),
                'booking_details' => [] // keep empty for now
            ];

            BookingHistory::create([
                'booking_id' => $booking->id,
                'data' => json_encode($dataForHistory),
                'action' => 'create',
                'message' => __('web.home.booking_created'),
            ]);

            // Handle PayPal URL error
            if (!isset($response['links'][1]['href'])) {
                return response()->json([
                    'code' => 500,
                    'message' => __('web.home.failed_to_create_paypal_link')
                ]);
            }

            return response()->json([
                'code' => 200,
                'message' => __('web.home.order_created_successfully'),
                'paypal_url' => $response['links'][1]['href']
            ]);
        }

        if ($request->payment_type == "stripe") {

            Stripe::setApiKey(config('services.stripe.secret'));
            $purchase_units = [];
            $currency_details = "USD"; // Fix currency

            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => "USD",
                        'product_data' => ['name' => "Rental Services"],
                        'unit_amount' => intval($request->total_price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('strip.payment.success') . "?session_id={CHECKOUT_SESSION_ID}",
            ]);

            $data = [
                "vehicle_id" => $request->input('vehicle_id'),
                "booking_status" => 4,
                "booking_by" => "user",
                "booking_date" => $formattedBookingDate,
                "start_datetime" => $startDatetime,
                "end_datetime" => $endDatetime,
                "pickup_location" => $pickup_location_id ?? null,
                "return_location" => $return_location_id ?? null,
                "delivery_location" => $pickup_location ?? null,
                "delivery_return_location" => $return_location ?? null,
                "delivery_type" => $request->rent_type,
                "rental_type" => $request->price_type_value,
                "security_deposit" => $request->input('security_deposit') ?? null,
                "booking_tariff" => $request->input('booking_tariff') ?? null,
                "driving_type" => $request->input('driving_type') ?? null,
                "no_of_passengers" => $request->input('no_person') ?? null,
                "no_of_days" => $noOfDays,
                "customer_id" => $authUser->id ?? 0,
                "driver_id" => $request->input('driver_id') ?? 0,
                "driver_price" => $request->input('driver_price_total'),
                "extra_service" => $request->input('extra_services'),
                "insurance" => $request->input('insurance'),
                "total_insurance_price" => $request->input('insurance_price_total'),
                "total_extra_service_price" => $request->input('extra_price_total'),
                "vehicle_price" => $request->input('vehicle_price'),
                "vehicle_total_price" => $request->input('vehicle_price_total'),
                "final_price" => $request->input('total_price'),
                "cancel_date" => $request->input('cancel_date') ?? null,
                "cancel_by" => $request->input('cancel_by') ?? null,
                "cancel_reason" => $request->input('cancel_reason') ?? null,
                "created_by" => $request->input('created_by') ?? null,
                "updated_by" => $request->input('updated_by') ?? null,
                "transaction_id" =>  $session->id,
                "payment_status" =>  1,
                "payment_type" =>  "stripe",
                "tax_val" =>  $request->tax_val ?? null,
            ];

            $booking = Booking::create($data);

            $reservationId = 'RES-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
            $booking->update(['reservation_id' => $reservationId]);

            $addData = [
                'booking_id' => $booking->id,
                'driver_first_name' => $request->driver_first_name,
                'driver_last_name' => $request->driver_last_name,
                'driver_age' => $request->driver_age,
                'driver_mobile_number' => $request->driver_mobile_number,
                'driver_licence' => $request->driver_licence,
                'driver_check' => $request->has('driver_check') ?? 1,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'no_person' => $request->no_person ?? 0,
                'company' => $request->company,
                'address' => $request->address,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'pincode' => $request->pincode,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'add_info' => $request->add_info,
                'terms' => $request->has('terms') ?? 1,
            ];

            $bookingInfo = BookingUserInfo::create($addData);

            $dataForHistory = [
                'bookings' => $booking->toArray(),
                'booking_details' => []
            ];

            BookingHistory::create([
                'booking_id' => $booking->id,
                'data' => json_encode($dataForHistory),
                'action' => 'create',
                'message' => __('web.home.booking_created'),
            ]);

            $stripURL = $session->url;

            return response()->json([
                'message' => __('web.home.order_created_successfully'),
                'stripurl' => $stripURL
            ]);
        }

        if ($request->payment_type == "wallet") {

            $totalAmount = WalletHistory::where('user_id', $authUser->id)->where('status', 'completed')->where('type', '1')->sum('amount');
            $totalAmountdebit = WalletHistory::where('user_id', $authUser->id)->where('status', 'completed')->where('type', '2')->sum('amount');
            $walletTotalAmount = $totalAmount - $totalAmountdebit;
            if ($walletTotalAmount < $request->input('total_price')) {
                return response()->json(['code' => 422, 'message' => __('web.home.insufficient_balance_in_wallet'), 'data' => []], 422);
            }

            $generateID = 'wallet' . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $data = [
                "vehicle_id" => $request->input('vehicle_id'),
                "booking_status" => 4,
                "booking_by" => "user",
                "booking_date" => $formattedBookingDate,
                "start_datetime" => $startDatetime,
                "end_datetime" => $endDatetime,
                "pickup_location" => $pickup_location_id ?? null,
                "return_location" => $return_location_id ?? null,
                "delivery_location" => $pickup_location ?? null,
                "delivery_return_location" => $return_location ?? null,
                "delivery_type" => $request->rent_type,
                "rental_type" => $request->price_type_value,
                "security_deposit" => $request->input('security_deposit') ?? null,
                "booking_tariff" => $request->input('booking_tariff')  ?? null,
                "driving_type" => $request->input('driving_type') ?? null,
                "no_of_passengers" => $request->input('no_person') ?? null,
                "no_of_days" => $noOfDays,
                "customer_id" => $authUser->id ?? 0,
                "driver_id" => $request->input('driver_id') ?? 0,
                "driver_price" => $request->input('driver_price_total'),
                "extra_service" => $request->input('extra_services'),
                "insurance" => $request->input('insurance'),
                "total_insurance_price" => $request->input('insurance_price_total'),
                "total_extra_service_price" => $request->input('extra_price_total'),
                "vehicle_price" => $request->input('vehicle_price'),
                "vehicle_total_price" => $request->input('vehicle_price_total'),
                "final_price" => $request->input('total_price'),
                "cancel_date" => $request->input('cancel_date') ?? null,
                "cancel_by" => $request->input('cancel_by') ?? null,
                "cancel_reason" => $request->input('cancel_reason') ?? null,
                "created_by" => $request->input('created_by') ?? null,
                "updated_by" => $request->input('updated_by') ?? null,
                "transaction_id" => $generateID,
                "payment_status" =>  1,
                "payment_type" =>  "wallet",
                "tax_val" =>  $request->tax_val ?? null,
            ];

            $booking = Booking::create($data);

            $dataForHistory = [
                'bookings' => $booking->toArray(),
                'booking_details' => [] // keep empty for now
            ];

            BookingHistory::create([
                'booking_id' => $booking->id,
                'data' => json_encode($dataForHistory),
                'action' => 'create',
                'message' => __('web.home.booking_created'),
            ]);

            $reservationId = 'RES-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

            $booking->update(['reservation_id' => $reservationId]);

            $addData = [
                'booking_id' => $booking->id,
                'driver_first_name' => $request->driver_first_name,
                'driver_last_name' => $request->driver_last_name,
                'driver_age' => $request->driver_age,
                'driver_mobile_number' => $request->driver_mobile_number,
                'driver_licence' => $request->driver_licence,
                'driver_check' => $request->has('driver_check') ?? 1,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'no_person' => $request->no_person ?? 0,
                'company' => $request->company,
                'address' => $request->address,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'pincode' => $request->pincode,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'add_info' => $request->add_info,
                'terms' => $request->has('terms') ?? 1,
            ];

            $bookingInfo = BookingUserInfo::create($addData);

            $walletData = [
                "user_id" => $authUser->id,
                "amount" => $request->input('total_price'),
                "payment_type" => "others",
                "status" => "Completed",
                "reference_id" => $booking->id,
                "transaction_id" => $booking->transaction_id,
                "transaction_date" => now(),
                "type" => 2,
            ];

            $wallet = WalletHistory::create($walletData);

            //send notification to admin
            $authUser = Auth::guard('web')->user();
            $vehicle = VehicleInfo::where('id', $request->vehicle_id)->first();
            $driver  = Driver::find($booking->driver_id);
            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $notifyData = [
                'user_name' => $authUser->name ?? '',
                'company_name' => $companyName,
                'email'     => $authUser->email ?? '',
                'phonenumber' => $authUser->phone_number ?? '',
                'vehicle_name' => $vehicle->name ?? "",
                'driver_name'  => $driver ? $driver->driver_name : "",
                'reservation_id' => $booking->reservation_id ?? "",
                'start_date'     => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                'end_date'       => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
                'pickup_location' => $booking->pickupLocation ? $booking->pickupLocation->name : "",
                'delivery_type'   => $booking->delivery_type ?? "",
                'rental_type'     => $booking->rental_type ?? "",
                'payment_type'    => $booking->payment_type ?? "",
                'payment_status'  => $booking->payment_status ?? "",
                'tototal_amount'  => $booking->final_price ?? ""
            ];
            if (rentalNotificationEnabled()) {
                $appAdmin = User::where('user_type', 1)->first();
                sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);

                sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
            }

            if ($booking) {
                return response()->json([
                    'code' => 200,
                    'message' => __('web.home.booking_successfully_created'),
                    'email' => $request->email,
                    'cod' => $booking->transaction_id,
                    'redirect_url' => route('payment.success.page', ['transaction_id' => $booking->transaction_id])
                ], 200);
            } else {
                return response()->json(['error' => __('web.home.failed_to_create_booking')], 500);
            }
        }
    }

    public function paypalPaymentSuccess(Request $request)
    {
        try {
            $response = $this->provider->capturePaymentOrder($request->get('token'));

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                Booking::where('transaction_id', $response['id'])->update(['payment_status' => 2]);
                $booking = Booking::where('transaction_id', $response['id'])->first();
                $authUser = Auth::guard('web')->user();
                $vehicle = VehicleInfo::where('id', $request->vehicle_id)->first();
                $driver  = Driver::find($booking->driver_id);
                $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
                $notifyData = [
                    'user_name' => $authUser->name ?? '',
                    'company_name' => $companyName,
                    'email'     => $authUser->email ?? '',
                    'phonenumber' => $authUser->phone_number ?? '',
                    'vehicle_name' => $vehicle->name ?? "",
                    'driver_name'  => $driver ? $driver->driver_name : "",
                    'reservation_id' => $booking->reservation_id ?? "",
                    'start_date'     => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                    'end_date'       => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
                    'pickup_location' => $booking->pickupLocation ? $booking->pickupLocation->name : "",
                    'delivery_type'   => $booking->delivery_type ?? "",
                    'rental_type'     => $booking->rental_type ?? "",
                    'payment_type'    => $booking->payment_type ?? "",
                    'payment_status'  => $booking->payment_status ?? "",
                    'tototal_amount'  => $booking->final_price ?? ""
                ];
                if (rentalNotificationEnabled()) {
                    $appAdmin = User::where('user_type', 1)->first();
                    sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);

                    sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
                }

                return redirect()->route('payment.success.page', ['transaction_id' => $response['id']]);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => __('web.home.payment_capture_failed'),
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function stripPaymentSuccess(Request $request)
    {
        try {
            Stripe::setApiKey(config('stripe.test.sk'));
            $sessionId = $request->get('session_id');

            Booking::where('transaction_id', $sessionId)->update(['payment_status' => 2]);
            $booking = Booking::where('transaction_id', $sessionId)->first();
            $authUser = Auth::guard('web')->user();
            $vehicle = VehicleInfo::where('id', $request->vehicle_id)->first();
            $driver  = Driver::find($booking->driver_id);
            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $notifyData = [
                'user_name' => $authUser->name ?? '',
                'company_name' => $companyName,
                'email'     => $authUser->email ?? '',
                'phonenumber' => $authUser->phone_number ?? '',
                'vehicle_name' => $vehicle->name ?? "",
                'driver_name'  => $driver ? $driver->driver_name : "",
                'reservation_id' => $booking->reservation_id ?? "",
                'start_date'     => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                'end_date'       => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
                'pickup_location' => $booking->pickupLocation ? $booking->pickupLocation->name : "",
                'delivery_type'   => $booking->delivery_type ?? "",
                'rental_type'     => $booking->rental_type ?? "",
                'payment_type'    => $booking->payment_type ?? "",
                'payment_status'  => $booking->payment_status ?? "",
                'tototal_amount'  => $booking->final_price ?? ""
            ];
            if (rentalNotificationEnabled()) {
                $appAdmin = User::where('user_type', 1)->first();
                sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);

                sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
            }
            return redirect()->route('payment.success.page', ['transaction_id' => $sessionId]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function transaction(Request $request)
    {

        $user = current_user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $sort = $request->input('sort', 'last_7_days');

        switch ($sort) {
            case 'last_30_days':
                $startDate = now()->subDays(30);
                break;
            case 'last_7_days':
            default:
                $startDate = now()->subDays(7);
                break;
        }

        $transactions = Booking::where('customer_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->with('vehicle')
            ->latest()
            ->limit(5)
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json(['message' => __('web.home.no_transaction_found')], 200);
        }

        $data = $transactions->map(function ($transaction) {
            return [
                'id'             => $transaction->id,
                'vehicle_name'   => $transaction->vehicle->name ?? 'N/A',
                'vehicle_image'  => $transaction->vehicle ? uploadedAsset($transaction->vehicle->vehicle_image) : uploadedAsset('default.png'),
                'rent_type' => ucfirst($transaction->rental_type),
                'status'         => $transaction->payment_status,
                'updated_at'     => $transaction->updated_at->format('d M Y, h:i A'),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
