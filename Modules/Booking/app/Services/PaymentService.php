<?php

namespace Modules\Booking\Services;

use App\Models\User;
use App\Models\WalletHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentService
{
    private $provider;
    private const DB_DATE_FORMAT = 'Y-m-d H:i:s';
    private const DEFAULT_ERROR_MESSAGE = 'An error occurred: ';

    public function __construct(private BookingBuilder $builder)
    {
        if (empty(env('PAYPAL_SANDBOX_CLIENT_ID')) || empty(env('PAYPAL_SANDBOX_CLIENT_SECRET'))) {
            $this->provider = null;
        } else {
            $this->provider = new PayPalClient();
            $this->provider->getAccessToken();
        }
    }

    public function userPayments(Request $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = currentUser();
        if (!$authUser) {
            return [
                'code'    => 401,
                'message' => 'Unauthorized',
            ];
        }

        $formattedBookingDate = Carbon::now()->format(self::DB_DATE_FORMAT);
        $pickupInput = ($request->input('pickup_date') ?? '') . ' ' . ($request->input('pickup_time') ?? '');
        $returnInput = ($request->input('return_date') ?? '') . ' ' . ($request->input('return_time') ?? '');

        $startDatetimeObj = Carbon::createFromFormat('d-m-Y H:i', $pickupInput);
        $endDatetimeObj = Carbon::createFromFormat('d-m-Y H:i', $returnInput);

        $startDatetime = $startDatetimeObj ? $startDatetimeObj->format(self::DB_DATE_FORMAT) : null;
        $endDatetime = $endDatetimeObj ? $endDatetimeObj->format(self::DB_DATE_FORMAT) : null;

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

        $data = [
            'pickup_location_id' => $pickup_location_id,
            'return_location_id' => $return_location_id,
            'pickup_location'    => $pickup_location,
            'return_location'    => $return_location,
            'noOfDays'           => $noOfDays,
            'startDatetime'      => $startDatetime,
            'endDatetime'        => $endDatetime,
            'formattedBookingDate' => $formattedBookingDate
        ];


        return match ($request->payment_type) {
            'cod' => $this->handleCodPayment($request, $authUser, $data),
            'paypal' => $this->handlePaypalPayment($request, $authUser, $data),
            'stripe' => $this->handleStripePayment($request, $authUser, $data),
            'wallet' => $this->handleWalletPayment($request, $authUser, $data),
            default => [
                'code' => 400,
                'message' => 'Invalid payment type',
            ],
        };
    }

    private function handleCodPayment(Request $request, User $authUser, array $formattedData): array
    {
        $generateID = 'COD' . str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $bookingData = $this->builder->buildBookingData($request, $authUser, $formattedData);
        $bookingData['booking_status'] = 4;
        $bookingData['transaction_id'] = $generateID;
        $bookingData['payment_status'] = 1;
        $bookingData['payment_type'] = "cod";

        $booking = $this->builder->createBookingWithInfo($bookingData, $this->builder->buildUserInfoData($request, new Booking()));

        //send notification
        $this->sendBookingNotification($booking, $request->vehicle_id);

        return [
            'code'         => 200,
            'message'      => __('web.home.booking_successfully_created'),
            'email'        => $request->email,
            'cod'          => $booking->transaction_id,
            'redirect_url' => route('payment.success.page', ['transaction_id' => $booking->transaction_id])
        ];
    }

    private function handlePaypalPayment(Request $request, User $authUser, array $formattedData): array
    {
        $result = [
            'success' => false,
            'code'    => 503,
            'message' => 'PayPal is currently unavailable. Please choose another payment method.',
        ];

        if (!$this->provider) {
            return $result;
        }

        $order['intent'] = 'CAPTURE';

        $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
        $currency = null;

        if ($currencySetting && $currencySetting->value) {
            $currency = Currency::find($currencySetting->value);
        }

        $currency_details = $currency->code ?? "usd";

        $allowedCurrencies = ['usd', 'inr', 'eur', 'aed'];
        $currency = strtolower(trim($currency_details));

        if (!in_array($currency, $allowedCurrencies)) {
            $result['code'] = 422;
            $result['message'] = 'Invalid currency selected. Please use a supported currency like USD, INR, EUR, etc.';
            return $result;
        }

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

        $order['purchase_units'] = [$unit];
        $order['application_context'] = [
            'return_url' => url('paypal-payment-success'),
            'cancel_url' => url('paypal-payment-failed')
        ];

        $response = $this->provider->createOrder($order);

        if (!is_array($response) || !array_key_exists('id', $response)) {
            return $result;
        }

        $bookingData = $this->builder->buildBookingData($request, $authUser, $formattedData);
        $bookingData['booking_status'] = 1;
        $bookingData['transaction_id'] = $response['id'];
        $bookingData['payment_status'] = 1;
        $bookingData['payment_type'] = "paypal";

        $this->builder->createBookingWithInfo($bookingData, $this->builder->buildUserInfoData($request, new Booking()));

        $result = [
            'code'       => 200,
            'message'    => __('web.home.order_created_successfully'),
            'paypal_url' => $response['links'][1]['href']
        ];

        return $result;
    }

    private function handleStripePayment(Request $request, User $authUser, array $formattedData): array
    {
        $stripeSecret = config('services.stripe.secret') ?? '';
        if (empty($stripeSecret)) {
            return [
                'code'    => 503,
                'success' => false,
                'message' => 'Stripe is currently unavailable. Please choose another payment method.'
            ];
        }
        Stripe::setApiKey(is_string($stripeSecret) ? $stripeSecret : '');

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

        $bookingData = $this->builder->buildBookingData($request, $authUser, $formattedData);
        $bookingData['booking_status'] = 1;
        $bookingData['transaction_id'] = $session->id;
        $bookingData['payment_status'] = 1;
        $bookingData['payment_type'] = "stripe";

        $this->builder->createBookingWithInfo($bookingData, $this->builder->buildUserInfoData($request, new Booking()));

        $stripURL = $session->url;
        return [
            'message'  => __('web.home.order_created_successfully'),
            'stripurl' => $stripURL
        ];
    }

    private function handleWalletPayment(Request $request, User $authUser, array $formattedData): array
    {
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

        $bookingData = $this->builder->buildBookingData($request, $authUser, $formattedData);
        $bookingData['booking_status'] = 4;
        $bookingData['transaction_id'] = $generateID;
        $bookingData['payment_status'] = 1;
        $bookingData['payment_type'] = "wallet";

        $booking = $this->builder->createBookingWithInfo($bookingData, $this->builder->buildUserInfoData($request, new Booking()));

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

        WalletHistory::create($walletData);

        //send notification
        $this->sendBookingNotification($booking, $request->vehicle_id);

        return [
            'code'         => 200,
            'message'      => __('web.home.booking_successfully_created'),
            'email'        => $request->email,
            'cod'          => $booking->transaction_id,
            'redirect_url' => route('payment.success.page', ['transaction_id' => $booking->transaction_id])
        ];
    }

    public function paypalPaymentSuccess(Request $request): array
    {
        try {
            $token = $request->get('token');
            $response = $this->provider->capturePaymentOrder(is_string($token) ? $token : '');
            $data = [];

            // Ensure $response is an array before accessing it as one
            if (is_array($response) && isset($response['status']) && $response['status'] == 'COMPLETED') {
                if (isset($response['id'])) {
                    Booking::where('transaction_id', $response['id'])->update([
                        'payment_status' => 2,
                        'booking_status' => 4,
                    ]);
                    $booking = Booking::where('transaction_id', $response['id'])->first();
                    $this->sendBookingNotification($booking, $request->vehicle_id);

                    $data = [
                        'redirect_url' => route('payment.success.page', ['transaction_id' => $response['id']])
                    ];
                } else {
                    $data = [
                        'code'    => 400,
                        'message' => __('web.home.payment_id_missing'),
                    ];
                }

            } else {
                $data = [
                    'code'    => 400,
                    'message' => __('web.home.payment_capture_failed'),
                ];
            }

            return $data;
        } catch (\Exception $e) {
           return [
                'code'    => 400,
                'message' => self::DEFAULT_ERROR_MESSAGE . $e->getMessage(),
                'error'   => $e
            ];
        }
    }

    public function paypalPaymentFailed(Request $request): array
    {
        try {
            $token = $request->get('token') ?? '';
            if (is_string($token)) {
                $this->provider->capturePaymentOrder($token);
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
                'message' => self::DEFAULT_ERROR_MESSAGE . $e->getMessage(),
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
            $this->sendBookingNotification($booking, $request->vehicle_id);

            return [
                'redirect_url' => route('payment.success.page', ['transaction_id' => $sessionId])
            ];

        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => self::DEFAULT_ERROR_MESSAGE . $e->getMessage(),
            ];
        }
    }

    private function sendBookingNotification(?Booking $booking, ?int $vehicleId): void
    {
        $authUser = Auth::guard('web')->user();
        $vehicle = VehicleInfo::find($vehicleId);
        $driver = $booking ? Driver::find($booking->driver_id) : null;
        $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';

        $getBookingField = fn($field) => $booking ? ($booking->$field ?? '') : '';
        $formatDateTimeField = fn($field) => $booking && $booking->$field ? formatDateTime($booking->$field) : '';
        $pickupLocationName = $booking && $booking->pickupLocation ? $booking->pickupLocation->name : '';

        $notifyData = [
            'user_name'       => getCurrentUserFullname($authUser->id ?? null) ?? '',
            'company_name'    => $companyName,
            'email'           => $authUser->email ?? '',
            'phonenumber'     => $authUser->phone_number ?? '',
            'vehicle_name'    => $vehicle->name ?? '',
            'driver_name'     => $driver->driver_name ?? '',
            'reservation_id'  => $getBookingField('reservation_id'),
            'start_date'      => $formatDateTimeField('start_datetime'),
            'end_date'        => $formatDateTimeField('end_datetime'),
            'pickup_location' => $pickupLocationName,
            'delivery_type'   => $getBookingField('delivery_type'),
            'rental_type'     => $getBookingField('rental_type'),
            'payment_type'    => $getBookingField('payment_type'),
            'payment_status'  => $getBookingField('payment_status'),
            'tototal_amount'  => $getBookingField('final_price'),
        ];

        try {
            // Notify admins
            if (rentalNotificationEnabled()) {
                $appAdmins = User::where('user_type', 1)->get();
                foreach ($appAdmins as $appAdmin) {
                    sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                }
            }

            // Notify user
            if (userNotificationsEnabled() && $authUser?->email) {
                sendNotification($authUser->email, 'booking-confirmation-to-user', $notifyData);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

}
