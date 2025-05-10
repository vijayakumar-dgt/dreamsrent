<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserBookings;
use App\Http\Resources\UserWishlist;
use App\Models\Country;
use App\Models\Notification;
use App\Models\WalletHistory;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\UserDevice;
use Carbon\Carbon;
use Illuminate\View\View;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\Enquiry;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            abort(403, 'Unauthorized access');
        }
        $totalBookingCount = Booking::where('customer_id', $user->id)
            ->where('deleted_at', null)->count();
        $totalWishlistCount = Wishlist::where('user_id', $user->id)->count();
        $totalCredit = WalletHistory::where('user_id', $user->id)
            ->where('status', 'Completed')
            ->where('type', '1')
            ->sum('amount');

        $totalDebit = WalletHistory::where('user_id', $user->id)
            ->where('status', 'Completed')
            ->where('type', '2')
            ->sum('amount');

        $totalBalance = $totalCredit - $totalDebit;
        $totalTransaction = Booking::where('customer_id', $user->id)
            ->where('deleted_at', null)->where('payment_status', 2)->sum('final_price');
        $currency = getDefaultCurrencySymbol();
        $seo_title = __('web.user.dashboard');
        return view(
            'frontend.user.dashboard',
            compact('totalBookingCount', 'totalWishlistCount', 'totalBalance', 'totalTransaction', 'currency', 'seo_title')
        );
    }

    public function bookings(Request $request): View
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            abort(403, 'Unauthorized access');
        }
        $totalBookingCount = Booking::where('customer_id', $user->id)
            ->where('deleted_at', null)->count();
        $seo_title = __('web.user.my_bookings');
        return view('frontend.user.bookings', compact('totalBookingCount', 'seo_title'));
    }
    public function ajaxLastBookings(Request $request): AnonymousResourceCollection
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            abort(403, 'Unauthorized access');
        }
        $bookings = Booking::where('customer_id', $user->id);

        if ($request->has('duration') && $request->duration != "") {
            $customFrom = $request->custom_from_date ?? "";
            $customTo   = $request->custom_to_date ?? "";
            $duration = $this->getDuration($request->duration, $customFrom, $customTo);

            if (!isset($duration['error']) && isset($duration['from'])) {
                $bookings->whereBetween('booking_date', [$duration['from'], $duration['to']]);
            }
        }

        if ($request->has('status') && $request->status != "") {
            if ($request->status === "upcomming") {
                $bookings->where('booking_date', '>', now());
            } else {
                $bookings->where('booking_status', $request->status);
            }
        }
        if ($request->has('sortby') && $request->sortby != "") {
            switch ($request->sortby) {
                case 'asc':
                    $bookings->orderBy('id', 'asc');
                    break;
                case 'desc':
                    $bookings->orderBy('id', 'desc');
                    break;
                case 'alphabet':
                    $bookings->with(['vehicle' => function ($query) {
                        $query->orderBy('name', 'asc');
                    }])->orderBy(
                        DB::raw('(SELECT name FROM vehicle_info WHERE vehicle_info.id = bookings.vehicle_id)'),
                        'asc'
                    );
                    break;
            }
        }

        $bookings = $bookings->orderBy('id', 'desc')->take(5)->get();
        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }
    public function ajaxBookings(Request $request): AnonymousResourceCollection
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            abort(403, 'Unauthorized access');
        }
        $bookings = Booking::where('customer_id', $user->id);

        if ($request->has('limit')) {
            $bookings->take($request->limit);
        }

        if ($request->has('duration') && $request->duration != "") {
            $customFrom = $request->custom_from_date ?? "";
            $customTo   = $request->custom_to_date ?? "";
            $duration = $this->getDuration($request->duration, $customFrom, $customTo);

            if (!isset($duration['error']) && isset($duration['from'])) {
                $bookings->whereBetween('start_datetime', [$duration['from'], $duration['to']]);
            }
        }

        if ($request->has('status') && $request->status != "") {
            if ($request->status === "upcomming") {
                $bookings->where('start_datetime', '>', now());
            } else {
                $bookings->where('booking_status', $request->status);
            }
        }
        if ($request->has('sortby') && $request->sortby != "") {
            switch ($request->sortby) {
                case 'asc':
                    $bookings->orderBy('id', 'asc');
                    break;
                case 'desc':
                    $bookings->orderBy('id', 'desc');
                    break;
                case 'alphabet':
                    $bookings->with(['vehicle' => function ($query) {
                        $query->orderBy('name', 'asc');
                    }])->orderBy(
                        DB::raw('(SELECT name FROM vehicle_info WHERE vehicle_info.id = bookings.vehicle_id)'),
                        'asc'
                    );
                    break;
            }
        }

        $bookings = $bookings->get();
        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }

     /**
     * @return array{from: string, to: string}|array{error: string}
     */
    public function getDuration(?string $duration, ?string $customFromDate = null, ?string $customToDate = null): array
    {
        switch ($duration) {
            case 'this_week':
                $duration = [
                    'from' => date('Y-m-d 00:00:00', strtotime('monday this week')),
                    'to' => date('Y-m-d 23:59:59', strtotime('sunday this week'))
                ];
                break;
            case 'this_month':
                $duration = [
                    'from' => date('Y-m-01 00:00:00'),
                    'to' => date('Y-m-t 23:59:59')
                ];
                break;
            case 'last30':
                $duration = [
                    'from' => date('Y-m-d 00:00:00', strtotime('-30 days')),
                    'to' => date('Y-m-d 23:59:59')
                ];
                break;
            case 'last60':
                $duration = [
                    'from' => date('Y-m-d 00:00:00', strtotime('-60 days')),
                    'to' => date('Y-m-d 23:59:59')
                ];
                break;
            case 'last7':
                $duration = [
                    'from' => date('Y-m-d 00:00:00', strtotime('-7 days')),
                    'to' => date('Y-m-d 23:59:59')
                ];
                break;
            case 'custom':
                if (!empty($customFromDate) && !empty($customToDate)) {
                    if (strtotime($customFromDate) > strtotime($customToDate)) {
                        return ['error' => 'Custom from date cannot be greater than to date'];
                    }

                    $fromTimestamp = strtotime($customFromDate);
                    $toTimestamp = strtotime($customToDate);

                    if ($fromTimestamp === false || $toTimestamp === false) {
                        return ['error' => 'Invalid custom date format'];
                    }

                    if ($fromTimestamp > $toTimestamp) {
                        return ['error' => 'Custom from date cannot be greater than to date'];
                    }

                    $duration = [
                        'from' => date('Y-m-d', $fromTimestamp) . ' 00:00:00',
                        'to' => date('Y-m-d', $toTimestamp) . ' 23:59:59'
                    ];
                } else {
                    return ['from' => '', 'to' => '', 'error' => 'Custom dates are required'];
                }
                break;
            default:
                $duration = ['from' => '', 'to' => '', 'error' => 'Invalid duration specified'];
        }

        return $duration;
    }

    public function bookingDetails(?int $id): JsonResponse
    {
        $booking = Booking::where('id', $id)->first();
        return response()->json([
            'status' => 'success',
            'data' => new UserBookings($booking)
        ]);
    }

    public function cancelRide(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $booking = Booking::find($request->id);
            if (!$booking) {
                return response()->json([
                    'status' => 'error',
                    'code'   => 404,
                    'message' => __('web.user.booking_not_found')
                ]);
            }

            if (!$booking instanceof \Modules\Booking\Models\Booking) {
                return response()->json([
                    'status' => 'error',
                    'code'   => 404,
                    'message' => __('web.user.booking_not_found')
                ]);
            }

            $bookingDetail = BookingDetail::where('booking_id', $booking->id)->first();
            $historyData = [
                'booking' => $booking->toArray(),
                'booking_detail' => $bookingDetail?->toArray() ?? []
            ];

            BookingHistory::create([
                'booking_id' => $booking->id,
                'action'     => 'cancel',
                'data'       => json_encode($historyData),
                'message'    => 'Reservation Cancelled'
            ]);

            $booking->update([
                'booking_status' => 6,
                'cancel_date'    => now(),
                'cancel_by'      => Auth::id(),
                'cancel_reason'  => $request->reason
            ]);

            DB::commit();

            if (rentalNotificationEnabled()) {
                try {
                    $authUser = Auth::user();
                    $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
                    $vehicle = VehicleInfo::find($booking->vehicle_id ?? '');
                    $driver  = Driver::find($booking->driver_id ?? '');
                    $appAdmin = User::where('user_type', 1)->first();

                    $notifyData = [
                        'user_name'       => $authUser->name ?? '',
                        'company_name'    => $companyName,
                        'email'           => $authUser->email ?? '',
                        'phonenumber'     => $authUser->phone_number ?? '',
                        'vehicle_name'    => $vehicle->name ?? "",
                        'driver_name'     => $driver->driver_name ?? "",
                        'reservation_id'  => $booking->reservation_id ?? "",
                        'start_date'      => formatDateTime($booking->start_datetime),
                        'end_date'        => formatDateTime($booking->end_datetime),
                        'pickup_location' => $booking->pickupLocation->name ?? "",
                        'delivery_type'   => $booking->delivery_type ?? "",
                        'rental_type'     => $booking->rental_type ?? "",
                        'payment_type'    => $booking->payment_type ?? "",
                        'payment_status'  => $booking->payment_status ?? "",
                        'tototal_amount'  => $booking->final_price ?? ""
                    ];


                    if ($appAdmin?->email) {
                        sendNotification($appAdmin->email, 'booking-cancelled-to-admin', $notifyData);
                    }

                    if (!empty($authUser->email)) {
                        sendNotification($authUser->email, 'booking-cancelled-to-user', $notifyData);
                    }
                } catch (\Throwable $ex) {
                }
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.reservation_cancelled')
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured')
            ]);
        }
    }

    public function completeRide(Request $request): JsonResponse
    {
        try {
            /** @var Booking|null $booking */
            $booking = Booking::find($request->id);
            $bookingDetail = BookingDetail::where('booking_id', $request->id)->first();
            $historyData = [
                'booking' => $booking ? $booking->toArray() : '',
                'booking_detail' => $bookingDetail ? $bookingDetail->toArray() : []
            ];

            BookingHistory::create([
                'booking_id' => $booking->id ?? '',
                'action'     => 'completed',
                'data'       => json_encode($historyData),
                'message'    => __('web.user.ride_completed')
            ]);

            if ($booking) {
                $booking->update([
                    'booking_status' => 5,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('web.user.ride_completed')
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.user.error_occured')
            ], 200);
        }
    }

    public function startRide(Request $request): JsonResponse
    {
        try {
             /** @var Booking|null $booking */
            $booking = Booking::find($request->id);
            $bookingDetail = BookingDetail::where('booking_id', $request->id)->first();
            $historyData = [
                'booking' => $booking ? $booking->toArray() : '',
                'booking_detail' => $bookingDetail ? $bookingDetail->toArray() : []
            ];

            BookingHistory::create([
                'booking_id' => $booking->id ?? '',
                'action'     => 'started',
                'data'       => json_encode($historyData),
                'message'    => __('web.user.ride_started')
            ]);

            if ($booking) {
                $booking->update([
                    'booking_status' => 1,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('web.user.ride_started')
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.user.error_occured')
            ], 200);
        }
    }

    public function deleteRide(Request $request): JsonResponse
    {
        try {
            /** @var Booking|null $booking */
            $booking = Booking::find($request->id);
            if ($booking) {
                $booking->delete();
            }
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('web.user.booking_deleted')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.user.error_occured')
            ], 200);
        }
    }

    public function wishlists(Request $request): View
    {
        $seo_title = __('web.user.wishlist');
        return view('frontend.user.wishlists', compact('seo_title'));
    }

    public function addToWishlist(Request $request): JsonResponse
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        try {
            /** @var VehicleInfo|null $vehicle */
            $vehicle = VehicleInfo::find($request->id);
            $wishlist = Wishlist::where('user_id', $authUserId)
                                    ->where('vehicle_id', $vehicle->id ?? '')
                                    ->first();
            if ($wishlist) {
                $wishlist->delete();
                return response()->json([
                    'status' => 'success',
                    'code'   => 200,
                    'message' => __('web.user.removed_from_wishlist')
                ]);
            } else {
                Wishlist::create([
                    'user_id' => $authUserId,
                    'vehicle_id' => $vehicle->id ?? ''
                ]);
                return response()->json([
                    'status' => 'success',
                    'code'   => 200,
                    'message' => __('web.user.added_to_wishlist')
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.user.error_occured')
            ], 200);
        }
    }

    public function ajaxWishlists(Request $request): JsonResponse
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        $wishlists = Wishlist::where('user_id', $authUserId)->get();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => UserWishlist::collection($wishlists)
        ]);
    }

    public function userprofilesettings(): View
    {
        $user = Auth::guard('web')->user();
        $countries = Country::where('status', 1)->get();
        $seo_title = __('web.user.profile');
        return view('frontend.user.usersettings', compact('seo_title', 'user', 'countries'));
    }

    public function userprofile(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->id,
                'user_phone' => 'required|numeric',
                'address_line' => 'nullable|string|max:255',
                'country' => 'required|integer|exists:countries,id',
                'state' => 'required|integer|exists:states,id',
                'city' => 'required|integer|exists:cities,id',
                'postal_code' => 'nullable|string|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => __('web.user.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::guard('web')->user();
            if ($user instanceof User) {
                $user->update([
                    'email' => $request->email,
                    'phone_number' => $request->user_phone,
                ]);
            }
            $profilePhoto = null;
            if ($request->hasFile('profile_photo')) {
                $folder = "profile";
                $profilePhoto = $request->file('profile_photo') ? uploadFile($request->file('profile_photo'), $folder) : null;
            }

            UserDetail::updateOrCreate(
                ['user_id' => $user?->id],
                [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mobile_number' => $request->user_phone,
                    'address' => $request->address_line,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'postal_code' => $request->postal_code,
                    'profile_image' => $profilePhoto ?? $user->userDetail->profile_image ?? null,
                ]
            );
            $profileImage = UserDetail::where('user_id', $user?->id)->value('profile_image');

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('web.user.profile_updated_successfully'),
                'data' => [
                    'profile_image' => uploadedAsset($profileImage, 'profile')
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('web.user.error_occured'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function userpreference(): View
    {
        $languages = Language::select('languages.language_id')
            ->with(['transLang' => function ($query) {
                $query->select('id', 'code', 'name');
            }])
            ->where('languages.status', 1)
            ->get();
        $id = Auth::guard('web')->user()->id ?? 0;
        $preference = User::select('language_id', 'region_id')->where('id', $id)->first();
        $countries = Country::select('id', 'name')->where('status', 1)->get();
        $seo_title = __('web.user.preferences');
        return view('frontend.user.preference', compact('languages', 'preference', 'countries', 'seo_title'));
    }

    public function userintegration(): View
    {
        return view('frontend.user.integration');
    }

    public function usernotification(): View
    {
        $seo_title = __('web.user.notifications');
        $user = Auth::guard('web')->user();
        return view('frontend.user.notification', compact('seo_title', 'user'));
    }

    public function updateNotificationSettings(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('web')->user();
            if ($user instanceof User) {
                $user->booking_confirmation = $request->booking_confirmation == "1" ? 1 : 0;
                $user->desktop_notifications = $request->desktop_notifications == "1" ? 1 : 0;
                $user->email_notifications = $request->email_notifications == "1" ? 1 : 0;
                $user->save();
            }
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('web.common.default_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('web.common.default_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function usersecurity(): View
    {
        $seo_title = __('web.user.security');
        return view('frontend.user.security', compact('seo_title'));
    }

    public function checkCurrentPassword(Request $request): JsonResponse
    {
        $password = $request->password;
        $user = Auth::guard('web')->user();
        if ($user && $user->password && Hash::check($password, $user->password)) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.current_password_correct')
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('web.user.current_password_incorrect')
            ]);
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('web.user.validation_failed'),
                'errors'  => $validator->errors()->toArray()
            ], 422);
        }

        $user = Auth::guard('web')->user();
        if (!$user || !$user->password || !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.current_password_incorrect')
            ], 500);
        }

        $user = Auth::guard('web')->user();
        if ($user instanceof User && $user->password) {
            $user->password = Hash::make($request->new_password);
            $user->last_password_changed_at = now();
            $user->save();
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.password_updated_successfully')
        ]);
    }
    public function getSecuritySettings(): JsonResponse
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        $userDevices = UserDevice::where('user_id', $authUserId)->orderBy('created_at', 'desc')
            ->take(5)->get()->map(function ($device) {
                return [
                    'id' => $device->id,
                    'device_type' => $device->device_type,
                    'browser' => $device->browser,
                    'os' => $device->os,
                    'ip_address' => $device->ip_address,
                    'location' => $device->location,
                    'date'     => Carbon::parse($device->created_at ?? '')->format('d M Y, h:i A')
                ];
            });
            $user = Auth::guard('web')->user();
        $response    = [
            'user' => Auth::guard('web')->user(),
            'last_password_changed_at' => Auth::guard('web')->check() && $user && $user->last_password_changed_at
                ? Carbon::parse($user->last_password_changed_at)->format('d M Y, h:i A')
                : "",
            'devices' => $userDevices
        ];
        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'data'    => $response
        ]);
    }

    public function logoutDevice(Request $request): JsonResponse
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        if ($request->isAll === "true") {
            UserDevice::where('user_id', $authUserId)->delete();
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.all_devices_removed')
            ]);
        } else {
            $device = UserDevice::find($request->id);
            if ($device) {
                if ($device instanceof UserDevice) {
                    $device->delete();
                }
                return response()->json([
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('web.user.device_removed_successfully')
                ]);
            }
        }
        return response()->json([
            'status'  => 'error',
            'code'    => 404,
            'message' => __('web.user.device_not_found')
        ], 404);
    }

    public function updatePreference(Request $request): JsonResponse
    {
        try {
            $id = Auth::guard('web')->user()->id ?? 0;
            $data = [];

            if ($request->has('language_id')) {
                $data['language_id'] = $request->language_id;
            } elseif ($request->has('region_id')) {
                $data['region_id'] = $request->region_id;
            }

            User::where('id', $id)->update($data);

            $language = TranslationLanguage::select('code')->where('id', $request->language_id)->first();
            session(['app_locale_user' => $language->code ?? 'en']);

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('web.user.preference_update_success')
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.common.default_update_error')
            ], 500);
        }
    }

    public function getPreferences(Request $request): JsonResponse
    {
        try {
            $id = Auth::guard('web')->user()->id ?? $request->user_id;
            $data = User::select('language_id', 'region_id')->where('id', $id)->first();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data'   => $data,
                'message' => __('web.common.default_retrieve_success'),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.common.default_retrieve_error')
            ], 500);
        }
    }

    public function reviews(Request $request): View
    {
        $seo_title = __('web.common.reviews');
        return view('frontend.user.reviews', compact('seo_title'));
    }

    public function storeEnquiry(Request $request): JsonResponse
    {
        try {
            Enquiry::create([
                'car_id' => $request->vehicle_id,
                'customer_name' => $request->enquiry_name,
                'email' => $request->enquiry_email,
                'phone' => $request->international_phone_number,
                'enquiry_date' => date('Y-m-d'),
                'enquiry_details' => $request->enquiry_message
            ]);

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('web.user.enquiry_submitted_successfully')
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.user.error_occured')
            ], 500);
        }
    }

    public function getNotifications(Request $request): JsonResponse
    {
        $notifications = [];
        $notificationCount = 0;
        if (Auth::guard('web')->check()) {
            $authUserId = Auth::guard('web')->user()->id ?? 0;
            $notifications = Notification::where('user_id', $authUserId)
                ->where('readed', 0)->orderBy('created_at', 'desc')->limit(10)->get();
            $notificationCount = Notification::where('user_id', $authUserId)->where('readed', 0)->count();
        }
        $html = view('frontend.user.notifications-popup', compact('notifications'))->render();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'html' => $html,
            'count' => $notificationCount
        ]);
    }
    public function markAllAsRead(Request $request): JsonResponse
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        if (
            Notification::where('user_id', $authUserId)
            ->where('readed', 0)->count() > 0
        ) {
            Notification::where('user_id', $authUserId)->update(['readed' => 1]);
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ], 200);
        }
    }

    public function payments(Request $request): View
    {
        $seo_title = __('web.user.payments');
        return view('frontend.user.payments', compact('seo_title'));
    }

    public function ajaxTransactions(Request $request): AnonymousResourceCollection
    {
        $bookingUsers = ['admin','user'];
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        $bookings = Booking::where('customer_id', $authUserId)->whereIn('booking_by', $bookingUsers);

        if ($request->has('limit')) {
            $bookings->take($request->limit);
        }

        if ($request->has('duration') && $request->duration != "") {
            $customFrom = $request->custom_from_date ?? "";
            $customTo   = $request->custom_to_date ?? "";
            $duration = $this->getDuration($request->duration, $customFrom, $customTo);

            if (!isset($duration['error']) && isset($duration['from'])) {
                $bookings->whereBetween('start_datetime', [$duration['from'], $duration['to']]);
            }
        }

        if ($request->has('status') && $request->status != "") {
            if ($request->status === "upcomming") {
                $bookings->where('start_datetime', '>', now());
            } else {
                $bookings->where('booking_status', $request->status);
            }
        }
        if ($request->has('sortby') && $request->sortby != "") {
            switch ($request->sortby) {
                case 'asc':
                    $bookings->orderBy('id', 'asc');
                    break;
                case 'desc':
                    $bookings->orderBy('id', 'desc');
                    break;
                case 'alphabet':
                    $bookings->with(['vehicle' => function ($query) {
                        $query->orderBy('name', 'asc');
                    }])->orderBy(
                        DB::raw('(SELECT name FROM vehicle_info WHERE vehicle_info.id = bookings.vehicle_id)'),
                        'asc'
                    );
                    break;
            }
        }

        $bookings = $bookings->get();
        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }

    public function notifications(Request $request): View | JsonResponse
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        $notifications = Notification::where('user_id', $authUserId)
            ->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            $view = view('frontend.user.partials.notification-items', compact('notifications'))->render();

            return response()->json([
                'html' => $view,
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'prev_page_url' => $notifications->previousPageUrl(),
                'next_page_url' => $notifications->nextPageUrl(),
                'count' => $notifications->total()
            ]);
        }

        return view('frontend.user.notifications', compact('notifications'));
    }

    public function markNotificationAsRead(Request $request): JsonResponse
    {
        Notification::where('id', $request->id)->update(['readed' => 1]);
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'message' => __('web.user.notification_marked_as_read')
        ], 200);
    }

    public function deleteNotification(Request $request): JsonResponse
    {
        Notification::where('id', $request->id)->delete();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'message' => __('web.user.notification_deleted')
        ], 200);
    }

    public function deleteAllNotification(Request $request): JsonResponse
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        Notification::where('user_id', $authUserId)->delete();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'message' => __('web.user.all_notofocations_deleted')
        ], 200);
    }
}
