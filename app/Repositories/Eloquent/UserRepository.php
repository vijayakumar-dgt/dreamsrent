<?php

namespace App\Repositories\Eloquent;

use App\Models\Country;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WalletHistory;
use App\Models\Wishlist;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\Enquiry;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Modules\GeneralSetting\Models\UserDevice;

class UserRepository implements UserRepositoryInterface
{
    private const VEHICLE_NAME_SUBQUERY = '(SELECT name FROM vehicle_info WHERE vehicle_info.id = bookings.vehicle_id)';
    private const UNAUTHORISED_ACCESS_MESSAGE = 'Unauthorized accessss';
    private const DATE_START_FORMAT = 'Y-m-d 00:00:00';
    private const DATE_END_FORMAT   = 'Y-m-d 23:59:59';

    public function getDashboardData(): array
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            abort(403, self::UNAUTHORISED_ACCESS_MESSAGE);
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

        return [
            'totalBookingCount'  => $totalBookingCount,
            'totalWishlistCount' => $totalWishlistCount,
            'totalBalance'       => $totalBalance,
            'totalTransaction'   => $totalTransaction,
            'currency'           => $currency,
            'seo_title'          => $seo_title
        ];
    }

    public function getUserBookings(): array
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            abort(403, self::UNAUTHORISED_ACCESS_MESSAGE);
        }
        $totalBookingCount = Booking::where('customer_id', $user->id)
            ->where('deleted_at', null)->count();
        $seo_title = __('web.user.my_bookings');
        return [
            'totalBookingCount' => $totalBookingCount,
            'seo_title'         => $seo_title
        ];
    }

    public function getAjaxLastBookings(Request $request): Collection
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            abort(403, self::UNAUTHORISED_ACCESS_MESSAGE);
        }
        $bookings = Booking::where('customer_id', $user->id)
            ->where('booking_by', '!=', 'quotation');

        if ($request->has('duration') && $request->duration != "") {
            $customFrom = $request->custom_from_date ?? "";
            $customTo = $request->custom_to_date ?? "";
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
                    $bookings->with([
                        'vehicle' => function ($query) {
                            $query->orderBy('name', 'asc');
                        }
                    ])->orderBy(
                        DB::raw(self::VEHICLE_NAME_SUBQUERY),
                        'asc'
                    );
                    break;
                default:
                    $bookings->orderBy('id', 'desc');
                    break;
            }
        }
        return $bookings->orderBy('id', 'desc')->take(5)->get();
    }

    public function getAjaxBookings(Request $request): Collection
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            abort(403, self::UNAUTHORISED_ACCESS_MESSAGE);
        }
        $bookings = Booking::where('customer_id', $user->id)
            ->where('booking_by', '!=', 'quotation');

        if ($request->has('limit')) {
            $bookings->take($request->limit);
        }

        if ($request->has('duration') && $request->duration != "") {
            $customFrom = $request->custom_from_date ?? "";
            $customTo = $request->custom_to_date ?? "";
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
                    $bookings->with([
                        'vehicle' => function ($query) {
                            $query->orderBy('name', 'asc');
                        }
                    ])->orderBy(
                        DB::raw(self::VEHICLE_NAME_SUBQUERY),
                        'asc'
                    );
                    break;
                default:
                    $bookings->orderBy('id', 'desc');
                    break;
            }
        }
        return $bookings->get();
    }

    public function getBookingDetails(int $id): object
    {
        return Booking::where('id', $id)->first();
    }

    public function cancelBooking(Request $request): array
    {
        DB::beginTransaction();
        try {
            $booking = Booking::find($request->id);

            if (!$booking instanceof \Modules\Booking\Models\Booking) {
                $response = [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('web.user.booking_not_found')
                ];
            } else {
                $bookingDetail = BookingDetail::where('booking_id', $booking->id)->first();
                $historyData = [
                    'booking'        => $booking->toArray(),
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

                if (rentalNotificationEnabled() !== 0) {
                    try {
                        $authUser = Auth::user();
                        $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
                        $vehicle = VehicleInfo::find($booking->vehicle_id ?? '');
                        $driver = Driver::find($booking->driver_id ?? '');
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
                        Log::error($ex->getMessage());
                    }
                }

                $response = [
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('web.user.reservation_cancelled')
                ];
            }

            return $response;

        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured')
            ];
        }
    }

    public function getDuration(?string $duration, ?string $customFromDate = null, ?string $customToDate = null): array
    {
        $result = ['from' => '', 'to' => ''];

        switch ($duration) {
            case 'this_week':
                $result = [
                    'from' => date(self::DATE_START_FORMAT, strtotime('monday this week')),
                    'to'   => date(self::DATE_END_FORMAT, strtotime('sunday this week'))
                ];
                break;

            case 'this_month':
                $result = [
                    'from' => date('Y-m-01 00:00:00'),
                    'to'   => date('Y-m-t 23:59:59')
                ];
                break;

            case 'last30':
                $result = [
                    'from' => date(self::DATE_START_FORMAT, strtotime('-30 days')),
                    'to'   => date(self::DATE_END_FORMAT)
                ];
                break;

            case 'last60':
                $result = [
                    'from' => date(self::DATE_START_FORMAT, strtotime('-60 days')),
                    'to'   => date(self::DATE_END_FORMAT)
                ];
                break;

            case 'last7':
                $result = [
                    'from' => date(self::DATE_START_FORMAT, strtotime('-7 days')),
                    'to'   => date(self::DATE_END_FORMAT)
                ];
                break;

            case 'custom':
                if (
                    $customFromDate && $customFromDate !== '0' &&
                    $customToDate   && $customToDate !== '0'
                ) {
                    $fromTimestamp = strtotime($customFromDate);
                    $toTimestamp   = strtotime($customToDate);

                    if ($fromTimestamp === false || $toTimestamp === false) {
                        $result['error'] = 'Invalid custom date format';
                    } elseif ($fromTimestamp > $toTimestamp) {
                        $result['error'] = 'Custom from date cannot be greater than to date';
                    } else {
                        $result = [
                            'from' => date('Y-m-d', $fromTimestamp) . ' 00:00:00',
                            'to'   => date('Y-m-d', $toTimestamp) . ' 23:59:59'
                        ];
                    }
                } else {
                    $result['error'] = 'Custom dates are required';
                }
                break;

            default:
                $result['error'] = 'Invalid duration specified';
        }

        return $result;
    }

    public function completeBooking(Request $request): array
    {
        DB::beginTransaction();
        try {
            /** @var Booking|null $booking */
            $booking = Booking::find($request->id);
            $bookingDetail = BookingDetail::where('booking_id', $request->id)->first();
            $historyData = [
                'booking'        => $booking ? $booking->toArray() : '',
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
            $response = [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.ride_completed')
            ];
            DB::commit();
            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured')
            ];
        }
    }

    public function startRide(Request $request)
    {
        DB::beginTransaction();
        try {
            /** @var Booking|null $booking */
            $booking = Booking::find($request->id);
            $bookingDetail = BookingDetail::where('booking_id', $request->id)->first();
            $historyData = [
                'booking'        => $booking ? $booking->toArray() : '',
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
            $response = [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.ride_started')
            ];
            DB::commit();
            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured')
            ];
        }
    }

    public function deleteRide(Request $request): array
    {
        DB::beginTransaction();
        try {
            /** @var Booking|null $booking */
            $booking = Booking::find($request->id);
            if ($booking) {
                $booking->delete();
            }
            $response = [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.booking_deleted')
            ];

            DB::commit();
            return $response;
        } catch (\Throwable $th) {
            $response = [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured')
            ];
            DB::rollBack();
            return $response;
        }
    }

    public function getWishlistData(): string
    {
        return __('web.user.wishlist');
    }

    public function addToWishlist(int $id)
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        try {
            /** @var VehicleInfo|null $vehicle */
            $vehicle = VehicleInfo::find($id);
            $wishlist = Wishlist::where('user_id', $authUserId)
                ->where('vehicle_id', $vehicle->id ?? '')
                ->first();
            if ($wishlist) {
                $wishlist->delete();
                $response = [
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('web.user.removed_from_wishlist')
                ];
            } else {
                Wishlist::create([
                    'user_id'    => $authUserId,
                    'vehicle_id' => $vehicle->id ?? ''
                ]);
                $response = [
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('web.user.added_to_wishlist')
                ];
            }

            return $response;
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured')
            ];
        }
    }

    public function getWishlistDataAjax(): Collection
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        return Wishlist::where('user_id', $authUserId)->get();
    }

    public function getProfileSettings()
    {
        $user = Auth::guard('web')->user();
        $countries = Country::where('status', 1)->get();
        $seo_title = __('web.user.profile');
        return [
            'user'      => $user,
            'countries' => $countries,
            'seo_title' => $seo_title
        ];
    }

    public function updateProfile(Request $request): array
    {
        try {
            $user = Auth::guard('web')->user();
            if ($user instanceof User) {
                $user->update([
                    'email'        => $request->email,
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
                    'first_name'    => $request->first_name,
                    'last_name'     => $request->last_name,
                    'mobile_number' => $request->user_phone,
                    'address'       => $request->address_line,
                    'country_id'    => $request->country,
                    'state_id'      => $request->state,
                    'city_id'       => $request->city,
                    'postal_code'   => $request->postal_code,
                    'profile_image' => $profilePhoto ?? $user->userDetail->profile_image ?? null,
                ]
            );
            $profileImage = UserDetail::where('user_id', $user?->id)->value('profile_image');
            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.profile_updated_successfully'),
                'data'    => [
                    'profile_image' => uploadedAsset($profileImage, 'profile')
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured'),
                'error'   => $e->getMessage()
            ];
        }
    }

    public function getPreferenceSettings(): array
    {
        $languages = Language::select('languages.language_id')
                    ->with([
                        'transLang' => function ($query) {
                            $query->select('id', 'code', 'name');
                        }
                    ])
                    ->where('languages.status', 1)
                    ->get();
        $id = Auth::guard('web')->user()->id ?? 0;
        $preference = User::select('language_id', 'region_id')->where('id', $id)->first();
        $countries = Country::select('id', 'name')->where('status', 1)->get();
        $seo_title = __('web.user.preferences');
        return [
            'languages'  => $languages,
            'preference' => $preference,
            'countries'  => $countries,
            'seo_title'  => $seo_title
        ];
    }

    public function getUserNotifications(): array
    {
        $seo_title = __('web.user.notifications');
        $user = Auth::guard('web')->user();
        return [
            'user'      => $user,
            'seo_title' => $seo_title
        ];
    }

    public function updateNotificationSettings(Request $request): array
    {
        try {
            $user = Auth::guard('web')->user();
            if ($user instanceof User) {
                $user->booking_confirmation = $request->booking_confirmation == "1" ? 1 : 0;
                $user->desktop_notifications = $request->desktop_notifications == "1" ? 1 : 0;
                $user->email_notifications = $request->email_notifications == "1" ? 1 : 0;
                $user->save();
            }
            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.common.default_update_success'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.common.default_update_error'),
                'error'   => $e->getMessage()
            ];
        }
    }

    public function checkCurrentPassword(Request $request): array
    {
        $password = $request->password;
        $user = Auth::guard('web')->user();
        if ($user && $user->password && Hash::check($password, $user->password)) {
            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.current_password_correct')
            ];
        } else {
            return [
                'status'  => 'error',
                'code'    => 422,
                'message' => __('web.user.current_password_incorrect')
            ];
        }
    }

    public function updatePassword(Request $request): array
    {
        $user = Auth::guard('web')->user();
        if (!$user || !$user->password || !Hash::check($request->current_password, $user->password)) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.current_password_incorrect')
            ];
        }

        $user = Auth::guard('web')->user();
        if ($user instanceof User && $user->password) {
            $user->password = Hash::make($request->new_password);
            $user->last_password_changed_at = now();
            $user->save();
        }

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.password_updated_successfully')
        ];
    }

    public function getSecuritySettings(): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        $userDevices = UserDevice::where('user_id', $authUserId)->orderBy('created_at', 'desc')
            ->take(5)->get()->map(function ($device) {
                return [
                    'id'          => $device->id,
                    'device_type' => $device->device_type,
                    'browser'     => $device->browser,
                    'os'          => $device->os,
                    'ip_address'  => $device->ip_address,
                    'location'    => $device->location,
                    'date'        => formatDateTime($device->created_at),
                ];
            });
        $user = Auth::guard('web')->user();
        return [
            'user'                     => Auth::guard('web')->user(),
            'last_password_changed_at' => Auth::guard('web')->check() && $user && $user->last_password_changed_at
                ? formatDateTime($user->last_password_changed_at)
                : "",
            'devices' => $userDevices
        ];
    }

    public function logoutDevice(Request $request): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        if ($request->isAll === "true") {
            UserDevice::where('user_id', $authUserId)->delete();
            $response = [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.all_devices_removed')
            ];
        } else {
            $device = UserDevice::find($request->id);
            if ($device) {
                if ($device instanceof UserDevice) {
                    $device->delete();
                }
                $response = [
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('web.user.device_removed_successfully')
                ];
            } else {
                $response = [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('web.user.device_not_found')
                ];
            }
        }

        return $response;
    }

    public function updatePreference(Request $request): array
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
            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.preference_update_success')
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.common.default_update_error')
            ];
        }
    }

    public function getPreferences(Request $request): array
    {
        try {
            $id = Auth::guard('web')->user()->id ?? $request->user_id;
            $data = User::select('language_id', 'region_id')->where('id', $id)->first();
            return [
                'status'  => 'success',
                'code'    => 200,
                'data'    => $data,
                'message' => __('web.common.default_retrieve_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.common.default_retrieve_error')
            ];
        }
    }

    public function storeEnquiry(Request $request): array
    {
        try {
            Enquiry::create([
                'car_id'          => $request->vehicle_id,
                'customer_name'   => $request->enquiry_name,
                'email'           => $request->enquiry_email,
                'phone'           => $request->international_phone_number,
                'enquiry_date'    => date('Y-m-d'),
                'enquiry_details' => $request->enquiry_message
            ]);
            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.enquiry_submitted_successfully')
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured')
            ];
        }
    }

    public function getNotifications(): array
    {
        $notifications = [];
        $notificationCount = 0;
        if (Auth::guard('web')->check()) {
            $authUserId = Auth::guard('web')->user()->id ?? 0;
            $notifications = Notification::where('user_id', $authUserId)
                ->where('readed', 0)->orderBy('created_at', 'desc')->limit(10)->get();
            $notificationCount = Notification::where('user_id', $authUserId)->where('readed', 0)->count();
        }
        $html = view('frontend.user.notifications-popup', ['notifications' => $notifications])->render();

        return [
            'status' => 'success',
            'code'   => 200,
            'html'   => $html,
            'count'  => $notificationCount
        ];
    }

    public function markAllAsRead(): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        if (
            Notification::where('user_id', $authUserId)
                ->where('readed', 0)->count() > 0
        ) {
            Notification::where('user_id', $authUserId)->update(['readed' => 1]);
            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ];
        } else {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ];
        }
    }

    public function getTransactionsAjax(Request $request): Collection
    {
        $bookingUsers = ['admin', 'user'];
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        $bookings = Booking::where('customer_id', $authUserId)->whereIn('booking_by', $bookingUsers);

        if ($request->has('limit')) {
            $bookings->take($request->limit);
        }

        if ($request->has('duration') && $request->duration != "") {
            $customFrom = $request->custom_from_date ?? "";
            $customTo = $request->custom_to_date ?? "";
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
                    $bookings->with([
                        'vehicle' => function ($query) {
                            $query->orderBy('name', 'asc');
                        }
                    ])->orderBy(
                        DB::raw(self::VEHICLE_NAME_SUBQUERY),
                        'asc'
                    );
                    break;
                default:
                    $bookings->orderBy('id', 'asc');
                    break;
            }
        }

        return $bookings->get();
    }

    public function notifications()
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        return Notification::where('user_id', $authUserId)
            ->orderBy('created_at', 'desc')->paginate(10);
    }

    public function markNotificationAsRead(int $id): array
    {
        Notification::where('id', $id)->update(['readed' => 1]);

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.notification_marked_as_read')
        ];
    }

    public function deleteNotification(int $id): array
    {
        Notification::where('id', $id)->delete();

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.notification_deleted')
        ];
    }

    public function deleteAllNotification(): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        Notification::where('user_id', $authUserId)->delete();

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.all_notofocations_deleted')
        ];
    }

    public function deleteAccount(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('web')->user();
        if (!$user) {
            return [
                'success' => false,
                'message' => __('admin.general_settings.user_not_found')
            ];
        }
        $user->delete();

        return [
            'success' => true,
            'message' => __('web.user.account_deleted_successfully')
        ];
    }
}
