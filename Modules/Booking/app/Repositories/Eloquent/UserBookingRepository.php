<?php

namespace Modules\Booking\Repositories\Eloquent;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Models\WalletHistory;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingHistory;
use Modules\Booking\Models\BookingUserInfo;
use Modules\Booking\Repositories\Contracts\UserBookingRepositoryInterface;
use Modules\Booking\Services\VehicleService;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\ExtraService;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleInsurance;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\InsuranceBenefit;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class UserBookingRepository implements UserBookingRepositoryInterface
{
    private $provider;

    public function __construct()
    {
        if (empty(env('PAYPAL_SANDBOX_CLIENT_ID')) || empty(env('PAYPAL_SANDBOX_CLIENT_SECRET'))) {
            $this->provider = null;
        } else {
            $this->provider = new PayPalClient();
            $this->provider->getAccessToken();
        }
    }

    public function getVehicleInfo(Request $request, string $slug): array|RedirectResponse
    {
        $vehicleService = app(VehicleService::class);
        if (!Auth::guard('web')->check()) {
            return $this->handleUnauthenticated($request);
        }

        $vehicle = $vehicleService->getVehicleBySlug($slug);
        $vehicleId = $vehicle?->id;

        $vehicleImageUrl   = uploadedAsset($vehicle->vehicle_image ?? '');
        $filteredPrices    = $vehicleService->getFilteredPrices($vehicle);
        $mainLocation      = $vehicleService->getMainLocation($vehicle);
        $allLocation       = $vehicleService->getAllLocations($vehicle);
        $locations         = $vehicleService->getPickupAndDeliveryLocations($request);

        $extraServices     = $vehicleService->getExtraServices($vehicleId);
        $extraServiceCount = $extraServices->count();

        $currencySymbol    = getDefaultCurrencySymbol();
        $vehicleInsurance  = $vehicleService->getVehicleInsurances($vehicleId);
        $countries         = Country::all();

        $driverInfo        = $vehicleService->getDriverInfo($vehicleId);
        $driverInfo_image  = $driverInfo?->image ? uploadedAsset($driverInfo->image, 'profile') : uploadedAsset('', 'profile');
        $driverInfo_ride   = $driverInfo ? Booking::where("driver_id", $driverInfo->id)->count() : 0;
        $driverInfo_price  = 100;

        $finalRate         = is_numeric($request->final_price_rate) ? (float) $request->final_price_rate : 0.0;
        $calculatedTaxes   = $vehicleService->calculateTaxes($finalRate);
        $totalTax          = array_sum(array_column($calculatedTaxes, 'amount'));
        $grandTotal        = $finalRate;

        $user              = Auth::guard('web')->user();
        $seo_title         = "User Booking";

        return [
            'slug'              => $slug,
            'user'              => $user,
            'vehicleId'         => $vehicleId,
            'vehicle'           => $vehicle,
            'vehicleImageUrl'   => $vehicleImageUrl,
            'mainLocation'      => $mainLocation,
            'filteredPrices'    => $filteredPrices,
            'extraServices'     => $extraServices,
            'extraServiceCount' => $extraServiceCount,
            'vehicleInsurance'  => $vehicleInsurance,
            'countries'         => $countries,
            'driverInfo'        => $driverInfo,
            'driverInfo_ride'   => $driverInfo_ride,
            'driverInfo_price'  => $driverInfo_price,
            'driverInfo_image'  => $driverInfo_image,
            'allLocation'       => $allLocation,
            'dlocation'         => $locations['dlocation'],
            'rlocation'         => $locations['rlocation'],
            'finalRate'         => $finalRate,
            'calculatedTaxes'   => $calculatedTaxes,
            'totalTax'          => $totalTax,
            'grandTotal'        => $grandTotal,
            'seo_title'         => $seo_title,
            'plocation'         => $locations['plocation'],
            'prlocation'        => $locations['prlocation'],
            'currencySymbol'    => $currencySymbol,
            'paypalStatus'      => $vehicleService->getPaymentStatus("paypal_status"),
            'stripeStatus'      => $vehicleService->getPaymentStatus("stripe_status"),
            'codStatus'         => $vehicleService->getPaymentStatus("cod_status"),
            'walletStatus'      => $vehicleService->getPaymentStatus("wallet_status"),
        ];
    }

    private function handleUnauthenticated(Request $request): array
    {
        session(['intended_url' => url()->current()]);
        session([
            'intended_booking' => [
                'slug' => $request->slug,
                'data' => $request->except('_token')
            ]
        ]);
        return [
            'redirect_url' => route('user-login')
        ];
    }

    public function getStates(int $country_id): Collection
    {
        return State::where('country_id', $country_id)
            ->where('status', 1)
            ->get(['id', 'name']);
    }

    public function getCities(int $state_id): Collection
    {
        return City::where('state_id', $state_id)
            ->where('status', 1)
            ->get(['id', 'name']);
    }

    public function checkBooking(Request $request): array
    {
        $pickupDatetime = $request->input('start_datetime');
        $returnDatetime = $request->input('end_datetime');
        $vehicleId = $request->input('vehicle_id');

        if (empty($pickupDatetime) || empty($returnDatetime) || empty($vehicleId)) {
            return  [
                'status'  => 'error',
                'code'    => 422,
                'message' => __('web.home.booking_required_fields')
            ];

        }

        $isUnavailable = Booking::where('vehicle_id', $vehicleId)
            ->where(function ($query) use ($pickupDatetime, $returnDatetime) {
                $query->where('start_datetime', '<', $returnDatetime)
                    ->where('end_datetime', '>', $pickupDatetime);
            })
            ->exists();

        if ($isUnavailable) {
            return [
                'status'  => 'error',
                'message' => __('web.home.vehicle_already_booked_for_selected_time'),
                'code'    => 200
            ];
        }
        return [
            'status'  => 'success',
            'message' => __('web.home.vehicle_available'),
            'code'    => 200
        ];
    }

    public function paymentSuccess(string $transaction_id): array
    {
        $booking = Booking::where('transaction_id', $transaction_id)->first();
        $startDateTime = formatDateTime($booking->start_datetime);
        $endDateTime = formatDateTime($booking->end_datetime);

        if (!$booking) {
            abort(404, 'Booking not found.');
        }

        $vehicleId = $booking->vehicle_id;

        /** @var mixed $rawExtraService */
        $rawExtraService = $booking->extra_service;

        $extraServices = [];

        /** @var array<array{id: int|string}> $extraServices */
        if (is_array($rawExtraService)) {
            $extraServices = $rawExtraService;
        } elseif (is_string($rawExtraService)) {
            $decoded = json_decode($rawExtraService, true);
            if (is_array($decoded)) {
                $extraServices = $decoded;
            }
        }

        $extraServiceIds = collect($extraServices)->pluck('id')->toArray();

        $vehicle = VehicleInfo::select('id', 'name', 'vehicle_image', 'main_location_id', 'vehicle_price')
            ->where('id', $vehicleId)
            ->first();
        $vehicleImageUrl = $vehicle ? asset('/storage/' . $vehicle->vehicle_image) : null;

        $mainLocation = null;
        if ($vehicle) {
            $mainLocation = Location::select('name', 'address')
                ->where('id', $vehicle->main_location_id)
                ->first();
        }

        $dLocation = Location::select('name', 'address')->where('id', $booking->pickup_location)->first();
        $rLocation = Location::select('name', 'address')->where('id', $booking->return_location)->first();

        $vehicleExtraServices = ExtraService::select('id', 'name')
            ->whereIn('id', $extraServiceIds)
            ->get();

        $vehicleExtraServicesWithPrice = $vehicleExtraServices->map(function ($service) use ($vehicleId) {
            $serviceData = VehicleExtraService::where('extra_service_id', $service->id)
                ->where('vehicle_id', $vehicleId)
                ->first(['price', 'value']);

            $service->price = $serviceData->price ?? 0;
            $service->value = $serviceData->value ?? null;

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
                $query->select('insurance_id', 'benefit');
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

        $driverInfo = null;
        if (!is_null($booking->driver_id)) {
            $driverInfo = Driver::select("id", "driver_name", "phone_number")
                ->where("id", $booking->driver_id)
                ->first();
        }

        if (is_null($driverInfo)) {
            $bookingUserInfo = BookingUserInfo::select("driver_first_name", "driver_last_name", "driver_mobile_number")
                ->where("booking_id", $booking->id)
                ->first();

            if ($bookingUserInfo) {
                $driverInfo = (object)[
                    'id'           => null,
                    'driver_name'  => trim($bookingUserInfo->driver_first_name . ' ' . $bookingUserInfo->driver_last_name),
                    'phone_number' => $bookingUserInfo->driver_mobile_number,
                ];
            }
        }

        return [
            'transaction_id'                => $transaction_id,
            'booking'                       => $booking,
            'vehicleId'                     => $vehicleId,
            'vehicle'                       => $vehicle,
            'vehicleImageUrl'               => $vehicleImageUrl,
            'dLocation'                     => $dLocation,
            'rLocation'                     => $rLocation,
            'mainLocation'                  => $mainLocation,
            'vehicleExtraServicesWithPrice' => $vehicleExtraServicesWithPrice,
            'vehicleInsurance'              => $vehicleInsurance,
            'driverInfo'                    => $driverInfo,
            'driverInfo_ride'               => $driverInfo_ride,
            'driverInfo_price'              => $driverInfo_price,
            'bookingInfo'                   => $bookingInfo,
            'currencySymbol'                => $currencySymbol,
            'startDateTime'                 => $startDateTime,
            'endDateTime'                   => $endDateTime
        ];
    }

    public function getBooking(string $transaction_id): object
    {
        return Booking::where('transaction_id', $transaction_id)->first();
    }

    public function userPayments(Request $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return [
                'code'    => 401,
                'message' => 'Unauthorized',
            ];
        }

        $formattedBookingDate = Carbon::now()->format('Y-m-d H:i:s');
        $pickupInput = ($request->input('pickup_date') ?? '') . ' ' . ($request->input('pickup_time') ?? '');
        $returnInput = ($request->input('return_date') ?? '') . ' ' . ($request->input('return_time') ?? '');


        $startDatetimeObj = Carbon::createFromFormat('d-m-Y H:i', $pickupInput);
        $endDatetimeObj = Carbon::createFromFormat('d-m-Y H:i', $returnInput);

        $startDatetime = $startDatetimeObj ? $startDatetimeObj->format('Y-m-d H:i:s') : null;
        $endDatetime = $endDatetimeObj ? $endDatetimeObj->format('Y-m-d H:i:s') : null;

        $diffInHours = $startDatetimeObj->floatDiffInHours($endDatetimeObj); // More precise in hours
        $noOfDays = max(1, ceil($diffInHours / 24)); // Minimum 1 day, then round up partial days

        $pickup_location_id = null;
        $return_location_id = null;
        $pickup_location = null;
        $return_location = null;

        if ($request->rent_type === 'delivery') {
            $pickup_location_id = $request->delivery_location;
            $return_location_id = $request->delivery_return_location;
            $pickup_location = $request->delivery_location;
            $return_location = $request->delivery_return_location;
        } elseif ($request->rent_type === 'self_pickup') {
            $pickup_location_id = $request->pickup_location;
            $return_location_id = $request->pickup_return_location;
            $pickup_location = $request->pickup_location;
            $return_location = $request->pickup_return_location;
        }

        if ($request->payment_type == "cod") {
            $generateID = 'COD' . str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $data = [
                "vehicle_id"                => $request->input('vehicle_id'),
                "booking_status"            => 4,
                "booking_by"                => "user",
                "booking_date"              => $formattedBookingDate,
                "start_datetime"            => $startDatetime,
                "end_datetime"              => $endDatetime,
                "pickup_location"           => $pickup_location_id,
                "return_location"           => $return_location_id,
                "delivery_location"         => $pickup_location ?? null,
                "delivery_return_location"  => $return_location ?? null,
                "delivery_type"             => $request->rent_type,
                "rental_type"               => $request->price_type_value,
                "security_deposit"          => $request->input('security_deposit') ?? null,
                "booking_tariff"            => $request->input('booking_tariff') ?? null,
                "driving_type"              => $request->input('driving_type') ?? null,
                "no_of_passengers"          => $request->input('no_person') ?? null,
                "no_of_days"                => $noOfDays,
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
                "transaction_id"            => $generateID,
                "payment_status"            => 1,
                "payment_type"              => "cod",
                "tax_val"                   => $request->tax_val ?? null,
            ];

            $booking = Booking::create($data);

            $dataForHistory = [
                'bookings'        => $booking->toArray(),
                'booking_details' => [] // keep empty for now
            ];

            BookingHistory::create([
                'booking_id' => $booking->id,
                'data'       => json_encode($dataForHistory),
                'action'     => 'create',
                'message'    => __('web.home.booking_created'),
            ]);

            $reservationId = 'RES-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);

            $booking->update(['reservation_id' => $reservationId]);

            $addData = [
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

            $bookingInfo = BookingUserInfo::create($addData);

            //send notification to admin
            $authUser = Auth::guard('web')->user();
            $vehicle = VehicleInfo::where('id', $request->vehicle_id)->first();
            $driver = Driver::find($booking->driver_id);
            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $notifyData = [
                'user_name'       => $authUser->name ?? '',
                'company_name'    => $companyName,
                'email'           => $authUser->email ?? '',
                'phonenumber'     => $authUser->phone_number ?? '',
                'vehicle_name'    => $vehicle->name ?? "",
                'driver_name'     => $driver ? $driver->driver_name : "",
                'reservation_id'  => $booking->reservation_id ?? "",
                'start_date'      => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                'end_date'        => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
                'pickup_location' => $booking->pickupLocation->name ?? '',
                'delivery_type'   => $booking->delivery_type ?? "",
                'rental_type'     => $booking->rental_type ?? "",
                'payment_type'    => $booking->payment_type ?? "",
                'payment_status'  => $booking->payment_status ?? "",
                'tototal_amount'  => $booking->final_price ?? ""
            ];
            try {
                if (rentalNotificationEnabled()) {
                    $appAdmin = User::where('user_type', 1)->first();

                    if ($appAdmin?->email) {
                        sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                    }
                }
                if (userNotificationsEnabled() && $authUser?->email) {
                    sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
            return [
                'code'         => 200,
                'message'      => __('web.home.booking_successfully_created'),
                'email'        => $request->email,
                'cod'          => $booking->transaction_id,
                'redirect_url' => route('payment.success.page', ['transaction_id' => $booking->transaction_id])
            ];
        }

        if ($request->payment_type == "paypal") {
            if (!$this->provider) {
                return [
                    'success' => false,
                    'code'    => 503,
                    'message' => 'PayPal is currently unavailable. Please choose another payment method.',
                ];
            }
            $order['intent'] = 'CAPTURE';

            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
            $currency = null;

            if ($currencySetting && $currencySetting->value) {
                $currency = Currency::find($currencySetting->value);
            }

            $currency_details = $currency->code ?? "usd";

            // dd($currency_details);

            $allowedCurrencies = ['usd', 'inr', 'eur', 'aed'];

            $currency = strtolower(trim($currency_details));
            if (!in_array($currency, $allowedCurrencies)) {
                return [
                    'code'    => 422,
                    'success' => false,
                    'message' => 'Invalid currency selected. Please use a supported currency like USD, INR, EUR, etc.',
                ];
            }

            $purchase_units = [];

            $unit = [
                'items' => [
                    [
                        'name'        => 'Rental System',
                        'quantity'    => 1,
                        'unit_amount' => [
                            'currency_code' => $currency_details,
                            'value'         => $request->total_price,
                        ]
                    ],
                ],
                'amount' => [
                    'currency_code' => $currency_details,
                    'value'         => $request->total_price,
                    'breakdown'     => [
                        'item_total' => [
                            'currency_code' => $currency_details,
                            'value'         => $request->total_price,
                        ],
                    ]
                ]
            ];

            $purchase_units[] = $unit;

            $order['purchase_units'] = $purchase_units;

            $order['application_context'] = [
                'return_url' => url('paypal-payment-success'),
                'cancel_url' => url('paypal-payment-failed')
            ];

            $response = $this->provider->createOrder($order);

            if (!is_array($response) || !array_key_exists('id', $response)) {
                return  [
                    'success' => false,
                    'code'    => 503,
                    'message' => 'PayPal is currently unavailable. Please choose another payment method.',
                ];
            }

            $data = [
                "vehicle_id"                => $request->input('vehicle_id'),
                "booking_status"            => 1,
                "booking_by"                => "user",
                "booking_date"              => $formattedBookingDate,
                "start_datetime"            => $startDatetime,
                "end_datetime"              => $endDatetime,
                "pickup_location"           => $pickup_location_id ?? null,
                "return_location"           => $return_location_id ?? null,
                "delivery_location"         => $pickup_location ?? null,
                "delivery_return_location"  => $return_location ?? null,
                "delivery_type"             => $request->rent_type,
                "rental_type"               => $request->price_type_value,
                "security_deposit"          => $request->input('security_deposit') ?? null,
                "booking_tariff"            => $request->input('booking_tariff') ?? null,
                "driving_type"              => $request->input('driving_type') ?? null,
                "no_of_passengers"          => $request->input('no_person') ?? null,
                "no_of_days"                => $noOfDays,
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
                "transaction_id"            => $response['id'],
                "payment_status"            => 1,
                "payment_type"              => "paypal",
                "tax_val"                   => $request->tax_val ?? null,
            ];

            $booking = Booking::create($data);

            $reservationId = 'RES-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
            $booking->update(['reservation_id' => $reservationId]);

            $addData = [
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

            $bookingInfo = BookingUserInfo::create($addData);

            $dataForHistory = [
                'bookings'        => $booking->toArray(),
                'booking_details' => [] // keep empty for now
            ];

            BookingHistory::create([
                'booking_id' => $booking->id,
                'data'       => json_encode($dataForHistory),
                'action'     => 'create',
                'message'    => __('web.home.booking_created'),
            ]);

            $approve_paypal_url = $response['links'][1]['href'];

            $response = [
                'code'       => 200,
                'message'    => __('web.home.order_created_successfully'),
                'paypal_url' => $approve_paypal_url
            ];

            return $response;
        }

        if ($request->payment_type == "stripe") {
            $stripeSecret = config('services.stripe.secret') ?? '';
            if (empty($stripeSecret)) {
                return [
                    'code'    => 503,
                    'success' => false,
                    'message' => 'Stripe is currently unavailable. Please choose another payment method.'
                ];
            }
            Stripe::setApiKey(is_string($stripeSecret) ? $stripeSecret : '');

            $purchase_units = [];

            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
            $currency = null;

            if ($currencySetting && $currencySetting->value) {
                $currency = Currency::find($currencySetting->value);
            }

            $currency_details = $currency->code ?? "usd";

            $allowedCurrencies = ['usd', 'inr', 'eur', 'aed'];

            $currency = strtolower(trim($currency_details));
            if (!in_array($currency, $allowedCurrencies)) {
                return [
                    'code'    => 422,
                    'success' => false,
                    'message' => 'Invalid currency selected. Please use a supported currency like USD, INR, EUR, etc.',
                ];
            }

            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency'     => $currency_details,
                        'product_data' => ['name' => "Rental Services"],
                        'unit_amount'  => intval((float) (is_numeric($request->input('total_price')) ? $request->input('total_price') : 0) * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => route('strip.payment.success') . "?session_id={CHECKOUT_SESSION_ID}",
            ]);

            $data = [
                "vehicle_id"                => $request->input('vehicle_id'),
                "booking_status"            => 1,
                "booking_by"                => "user",
                "booking_date"              => $formattedBookingDate,
                "start_datetime"            => $startDatetime,
                "end_datetime"              => $endDatetime,
                "pickup_location"           => $pickup_location_id ?? null,
                "return_location"           => $return_location_id ?? null,
                "delivery_location"         => $pickup_location ?? null,
                "delivery_return_location"  => $return_location ?? null,
                "delivery_type"             => $request->rent_type,
                "rental_type"               => $request->price_type_value,
                "security_deposit"          => $request->input('security_deposit') ?? null,
                "booking_tariff"            => $request->input('booking_tariff') ?? null,
                "driving_type"              => $request->input('driving_type') ?? null,
                "no_of_passengers"          => $request->input('no_person') ?? null,
                "no_of_days"                => $noOfDays,
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
                "transaction_id"            => $session->id,
                "payment_status"            => 1,
                "payment_type"              => "stripe",
                "tax_val"                   => $request->tax_val ?? null,
            ];

            $booking = Booking::create($data);

            $reservationId = 'RES-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
            $booking->update(['reservation_id' => $reservationId]);

            $addData = [
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

            $bookingInfo = BookingUserInfo::create($addData);

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

            $stripURL = $session->url;
            return [
                'message'  => __('web.home.order_created_successfully'),
                'stripurl' => $stripURL
            ];

        }

        if ($request->payment_type == "wallet") {
            $totalAmount = WalletHistory::where('user_id', $authUser->id)
                ->where('status', 'completed')
                ->where('type', '1')
                ->sum('amount');

            $totalAmountdebit = WalletHistory::where('user_id', $authUser->id)
                ->where('status', 'completed')
                ->where('type', '2')
                ->sum('amount');

            $walletTotalAmount = $totalAmount - $totalAmountdebit;

            if ($walletTotalAmount < $request->input('total_price')) {
                return [
                    'code'    => 422,
                    'message' => __('web.home.insufficient_balance_in_wallet'),
                    'data'    => []
                ];
            }

            $generateID = 'wallet' . str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $data = [
                "vehicle_id"                => $request->input('vehicle_id'),
                "booking_status"            => 4,
                "booking_by"                => "user",
                "booking_date"              => $formattedBookingDate,
                "start_datetime"            => $startDatetime,
                "end_datetime"              => $endDatetime,
                "pickup_location"           => $pickup_location_id ?? null,
                "return_location"           => $return_location_id ?? null,
                "delivery_location"         => $pickup_location ?? null,
                "delivery_return_location"  => $return_location ?? null,
                "delivery_type"             => $request->rent_type,
                "rental_type"               => $request->price_type_value,
                "security_deposit"          => $request->input('security_deposit') ?? null,
                "booking_tariff"            => $request->input('booking_tariff') ?? null,
                "driving_type"              => $request->input('driving_type') ?? null,
                "no_of_passengers"          => $request->input('no_person') ?? null,
                "no_of_days"                => $noOfDays,
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
                "transaction_id"            => $generateID,
                "payment_status"            => 1,
                "payment_type"              => "wallet",
                "tax_val"                   => $request->tax_val ?? null,
            ];

            $booking = Booking::create($data);

            $dataForHistory = [
                'bookings'        => $booking->toArray(),
                'booking_details' => [] // keep empty for now
            ];

            BookingHistory::create([
                'booking_id' => $booking->id,
                'data'       => json_encode($dataForHistory),
                'action'     => 'create',
                'message'    => __('web.home.booking_created'),
            ]);

            $reservationId = 'RES-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);

            $booking->update(['reservation_id' => $reservationId]);

            $addData = [
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

            $bookingInfo = BookingUserInfo::create($addData);

            $walletData = [
                "user_id"          => $authUser->id,
                "amount"           => $request->input('total_price'),
                "payment_type"     => "others",
                "status"           => "Completed",
                "reference_id"     => $booking->id,
                "transaction_id"   => $booking->transaction_id,
                "transaction_date" => now(),
                "type"             => 2,
            ];

            $wallet = WalletHistory::create($walletData);

            //send notification to admin
            $authUser = Auth::guard('web')->user();
            $vehicle = VehicleInfo::where('id', $request->vehicle_id)->first();
            $driver = Driver::find($booking->driver_id);
            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $notifyData = [
                'user_name'       => $authUser->name ?? '',
                'company_name'    => $companyName,
                'email'           => $authUser->email ?? '',
                'phonenumber'     => $authUser->phone_number ?? '',
                'vehicle_name'    => $vehicle->name ?? "",
                'driver_name'     => $driver ? $driver->driver_name : "",
                'reservation_id'  => $booking->reservation_id ?? "",
                'start_date'      => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                'end_date'        => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
                'pickup_location' => $booking->pickupLocation ? $booking->pickupLocation->name : "",
                'delivery_type'   => $booking->delivery_type ?? "",
                'rental_type'     => $booking->rental_type ?? "",
                'payment_type'    => $booking->payment_type ?? "",
                'payment_status'  => $booking->payment_status ?? "",
                'tototal_amount'  => $booking->final_price ?? ""
            ];
            try {
                if (rentalNotificationEnabled()) {
                    $appAdmin = User::where('user_type', 1)->first();

                    if ($appAdmin) {
                        sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                    }
                }
                if (userNotificationsEnabled() && $authUser && $authUser->email) {
                    sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
            return [
                'code'         => 200,
                'message'      => __('web.home.booking_successfully_created'),
                'email'        => $request->email,
                'cod'          => $booking->transaction_id,
                'redirect_url' => route('payment.success.page', ['transaction_id' => $booking->transaction_id])
            ];

        }
    }

    public function paypalPaymentSuccess(Request $request): array
    {
        try {
            $token = $request->get('token');
            $response = $this->provider->capturePaymentOrder(is_string($token) ? $token : '');

            // Ensure $response is an array before accessing it as one
            if (is_array($response) && isset($response['status']) && $response['status'] == 'COMPLETED') {
                if (isset($response['id'])) {
                    Booking::where('transaction_id', $response['id'])->update([
                        'payment_status' => 2,
                        'booking_status' => 4,
                    ]);
                    $booking = Booking::where('transaction_id', $response['id'])->first();
                    $authUser = Auth::guard('web')->user();
                    $vehicle = VehicleInfo::where('id', $request->vehicle_id)->first();
                    $driver = $booking ? Driver::find($booking->driver_id) : null;
                    $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
                    $notifyData = [
                        'user_name'       => $authUser->name ?? '',
                        'company_name'    => $companyName,
                        'email'           => $authUser->email ?? '',
                        'phonenumber'     => $authUser->phone_number ?? '',
                        'vehicle_name'    => $vehicle->name ?? "",
                        'driver_name'     => $driver ? $driver->driver_name : "",
                        'reservation_id'  => $booking->reservation_id ?? "",
                        'start_date'      => ($booking && $booking->start_datetime) ? formatDateTime($booking->start_datetime) : '',
                        'end_date'        => ($booking && $booking->end_datetime) ? formatDateTime($booking->end_datetime) : '',
                        'pickup_location' => ($booking && $booking->pickupLocation) ? $booking->pickupLocation->name : '',
                        'delivery_type'   => $booking->delivery_type ?? "",
                        'rental_type'     => $booking->rental_type ?? "",
                        'payment_type'    => $booking->payment_type ?? "",
                        'payment_status'  => $booking->payment_status ?? "",
                        'tototal_amount'  => $booking->final_price ?? ""
                    ];
                    try {
                        if (rentalNotificationEnabled()) {
                            $appAdmin = User::where('user_type', 1)->first();

                            if ($appAdmin) {
                                sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                            }
                        }
                        if (userNotificationsEnabled() && $authUser && $authUser->email) {
                            sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
                        }
                    } catch (\Exception $e) {
                    }
                    $response = [
                        'redirect_url' => route('payment.success.page', ['transaction_id' => $response['id']])
                    ];

                    return $response;
                }
                $response = [
                    'code'    => 400,
                    'message' => __('web.home.payment_id_missing'),
                ];
                return $response;
            } else {
                $response = [
                    'code'    => 400,
                    'message' => __('web.home.payment_capture_failed'),
                ];
                return $response;
            }
        } catch (\Exception $e) {
           return [
                'code'    => 400,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'error'   => $e
            ];
        }
    }

    public function paypalPaymentFailed(Request $request): array
    {
        try {
            $token = $request->get('token') ?? '';
            if (is_string($token)) {
                $response = $this->provider->capturePaymentOrder($token);
            }
            Booking::where('transaction_id', $request->token)
                ->update([
                    'payment_status' => 3,
                    'booking_status' => 3,  // Set the booking status to 3 (Failed)
                ]);
           return [
                'redirect_url' => route('payment.success.fail', ['transaction_id' => $request->token])
            ];

        } catch (\Exception $e) {
            Booking::where('transaction_id', $request->get('token'))
                ->update([
                    'payment_status' => 3,
                    'booking_status' => 3,  // Set the booking status to 3 (Failed)
                ]);

           return [
                'code'    => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    public function stripPaymentSuccess(Request $request): array
    {
        try {
            $stripeSecret = config('stripe.test.sk', '');
            Stripe::setApiKey(is_string($stripeSecret) ? $stripeSecret : '');
            $sessionId = $request->get('session_id');

            Booking::where('transaction_id', $sessionId)->update([
                'payment_status' => 2,
                'booking_status' => 4,
            ]);
            $booking = Booking::where('transaction_id', $sessionId)->first();
            $authUser = Auth::guard('web')->user();
            $vehicle = VehicleInfo::where('id', $request->vehicle_id)->first();
            $driver = $booking ? Driver::find($booking->driver_id) : null;
            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $notifyData = [
                'user_name'       => $authUser->name ?? '',
                'company_name'    => $companyName,
                'email'           => $authUser->email ?? '',
                'phonenumber'     => $authUser->phone_number ?? '',
                'vehicle_name'    => $vehicle->name ?? '',
                'driver_name'     => $driver ? $driver->driver_name : '',
                'reservation_id'  => $booking->reservation_id ?? '',
                'start_date'      => ($booking && $booking->start_datetime) ? formatDateTime($booking->start_datetime) : '',
                'end_date'        => ($booking && $booking->end_datetime) ? formatDateTime($booking->end_datetime) : '',
                'pickup_location' => $booking->pickupLocation->name ?? '',
                'delivery_type'   => $booking->delivery_type ?? '',
                'rental_type'     => $booking->rental_type ?? '',
                'payment_type'    => $booking->payment_type ?? '',
                'payment_status'  => $booking->payment_status ?? '',
                'tototal_amount'  => $booking->final_price ?? ''
            ];
            try {
                if (rentalNotificationEnabled()) {
                    $appAdmin = User::where('user_type', 1)->first();

                    if ($appAdmin) {
                        sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                    }
                }
                if (userNotificationsEnabled() && $authUser) {
                    sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
                }
            } catch (\Exception $e) {
            }
            return [
                'redirect_url' => route('payment.success.page', ['transaction_id' => $sessionId])
            ];

        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];

        }
    }

    public function getTransaction(Request $request): array
    {
        /** @var \App\Models\User|null $user */
        $user = current_user();

        if (!$user) {
            return [
                'error' => 'Unauthorized',
                'code'  => 401
            ];
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
            ->where('booking_by', 'user')
            ->with('vehicle')
            ->latest()
            ->limit(3)
            ->get();

        if ($transactions->isEmpty()) {
            return [
                'message' => __('web.home.no_transaction_found'),
                'code'    => 200
            ];
        }

        $data = $transactions->map(function ($transaction) {
            $imgpath = $transaction->vehicle ? $transaction->vehicle->vehicle_image : '';
            if ($imgpath) {
                $filename = basename($imgpath);
                $newpath = 'vehicles/images/thumbnail/' . $filename;
                $file = public_path('storage/' . $newpath);
                if (file_exists($file)) {
                    $transaction->vehicle->vehicle_image = $newpath;
                }
            }
            return [
                'id'            => $transaction->id,
                'vehicle_name'  => $transaction->vehicle->name ?? 'N/A',
                'vehicle_image' => $transaction->vehicle
                    ? uploadedAsset($transaction->vehicle->vehicle_image)
                    : uploadedAsset('default.png'),
                'rent_type'     => ucfirst((string) ($transaction->rental_type ?? '')),
                'status'        => $transaction->payment_status,
                'updated_at'    => $transaction->updated_at->format('d M Y, h:i A'),
            ];
        });

        return [
            'code'   => 200,
            'data'   => $data,
            'status' => 'success'
        ];
    }

    public function getBenefits(Request $request)
    {
        $insuranceId = $request->input('id');

        return InsuranceBenefit::where('insurance_id', $insuranceId)
            ->select('benefit')
            ->get();
    }

}
