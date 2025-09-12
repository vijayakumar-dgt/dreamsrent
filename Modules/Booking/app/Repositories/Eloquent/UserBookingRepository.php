<?php

namespace Modules\Booking\Repositories\Eloquent;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
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
        $driverInfo_ride = $driverInfo ? Booking::where("driver_id", $driverInfo->id)->count() : 0;
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

    public function getTransaction(Request $request): array
    {
        /** @var \App\Models\User|null $user */
        $user = currentUser();

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
