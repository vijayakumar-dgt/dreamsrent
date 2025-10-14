<?php

namespace App\Repositories\Eloquent;

use App\Models\Wishlist;
use App\Models\WalletHistory;
use App\Repositories\Contracts\UserBookingRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\GeneralSetting;

class UserBookingRepository implements UserBookingRepositoryInterface
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
            ->where('deleted_at', null)
            ->count();
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
            ->where('deleted_at', null)
            ->where('payment_status', 2)
            ->sum('final_price');
        $currency = getDefaultCurrencySymbol();
        $seo_title = __('web.user.dashboard');

        return [
            'totalBookingCount'  => $totalBookingCount,
            'totalWishlistCount' => $totalWishlistCount,
            'totalBalance'       => $totalBalance,
            'totalTransaction'   => $totalTransaction,
            'currency'           => $currency,
            'seo_title'          => $seo_title,
        ];
    }

    public function getUserBookings(): array
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            abort(403, self::UNAUTHORISED_ACCESS_MESSAGE);
        }

        $totalBookingCount = Booking::where('customer_id', $user->id)
            ->where('deleted_at', null)
            ->count();
        $seo_title = __('web.user.my_bookings');

        return [
            'totalBookingCount' => $totalBookingCount,
            'seo_title'         => $seo_title,
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

        $this->applyDurationFilter($bookings, $request, 'booking_date');
        $this->applyStatusFilter($bookings, $request, 'booking_date');
        $this->applySorting($bookings, $request);

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

        $this->applyDurationFilter($bookings, $request);
        $this->applyStatusFilter($bookings, $request);
        $this->applySorting($bookings, $request);

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

            if (!$booking instanceof Booking) {
                $response = [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('web.user.booking_not_found'),
                ];
            } else {
                $bookingDetail = BookingDetail::where('booking_id', $booking->id)->first();
                $historyData = [
                    'booking'        => $booking->toArray(),
                    'booking_detail' => $bookingDetail?->toArray() ?? [],
                ];

                BookingHistory::create([
                    'booking_id' => $booking->id,
                    'action'     => 'cancel',
                    'data'       => json_encode($historyData),
                    'message'    => 'Reservation Cancelled',
                ]);

                $booking->update([
                    'booking_status' => 6,
                    'cancel_date'    => now(),
                    'cancel_by'      => Auth::id(),
                    'cancel_reason'  => $request->reason,
                ]);

                DB::commit();

                if (rentalNotificationEnabled() !== 0) {
                    $this->sendCancellationNotifications($booking);
                }

                $response = [
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('web.user.reservation_cancelled'),
                ];
            }

            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured'),
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
                    'to'   => date(self::DATE_END_FORMAT, strtotime('sunday this week')),
                ];
                break;

            case 'this_month':
                $result = [
                    'from' => date('Y-m-01 00:00:00'),
                    'to'   => date('Y-m-t 23:59:59'),
                ];
                break;

            case 'last30':
                $result = [
                    'from' => date(self::DATE_START_FORMAT, strtotime('-30 days')),
                    'to'   => date(self::DATE_END_FORMAT),
                ];
                break;

            case 'last60':
                $result = [
                    'from' => date(self::DATE_START_FORMAT, strtotime('-60 days')),
                    'to'   => date(self::DATE_END_FORMAT),
                ];
                break;

            case 'last7':
                $result = [
                    'from' => date(self::DATE_START_FORMAT, strtotime('-7 days')),
                    'to'   => date(self::DATE_END_FORMAT),
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
                            'to'   => date('Y-m-d', $toTimestamp) . ' 23:59:59',
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
            $booking = Booking::find($request->id);
            $bookingDetail = BookingDetail::where('booking_id', $request->id)->first();
            $historyData = [
                'booking'        => $booking ? $booking->toArray() : '',
                'booking_detail' => $bookingDetail ? $bookingDetail->toArray() : [],
            ];

            BookingHistory::create([
                'booking_id' => $booking->id ?? '',
                'action'     => 'completed',
                'data'       => json_encode($historyData),
                'message'    => __('web.user.ride_completed'),
            ]);

            if ($booking) {
                $booking->update([
                    'booking_status' => 5,
                ]);
            }

            DB::commit();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.ride_completed'),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured'),
            ];
        }
    }

    public function startRide(Request $request): array
    {
        DB::beginTransaction();

        try {
            $booking = Booking::find($request->id);
            $bookingDetail = BookingDetail::where('booking_id', $request->id)->first();
            $historyData = [
                'booking'        => $booking ? $booking->toArray() : '',
                'booking_detail' => $bookingDetail ? $bookingDetail->toArray() : [],
            ];

            BookingHistory::create([
                'booking_id' => $booking->id ?? '',
                'action'     => 'started',
                'data'       => json_encode($historyData),
                'message'    => __('web.user.ride_started'),
            ]);

            if ($booking) {
                $booking->update([
                    'booking_status' => 1,
                ]);
            }

            DB::commit();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.ride_started'),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured'),
            ];
        }
    }

    public function deleteRide(Request $request): array
    {
        DB::beginTransaction();

        try {
            $booking = Booking::find($request->id);

            if ($booking) {
                $booking->delete();
            }

            DB::commit();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.booking_deleted'),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured'),
            ];
        }
    }

    public function getTransactionsAjax(Request $request): Collection
    {
        $bookingUsers = ['admin', 'user'];
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        $bookings = Booking::where('customer_id', $authUserId)
            ->whereIn('booking_by', $bookingUsers);

        if ($request->has('limit')) {
            $bookings->take($request->limit);
        }

        $this->applyDurationFilter($bookings, $request, 'start_datetime');
        $this->applyStatusFilter($bookings, $request, 'start_datetime');
        $this->applySorting($bookings, $request);

        return $bookings->get();
    }

    private function applyDurationFilter($query, Request $request, string $dateColumn = 'start_datetime'): void
    {
        if ($request->has('duration') && $request->duration != "") {
            $customFrom = $request->custom_from_date ?? "";
            $customTo = $request->custom_to_date ?? "";
            $duration = $this->getDuration($request->duration, $customFrom, $customTo);

            if (!isset($duration['error']) && isset($duration['from'])) {
                $query->whereBetween($dateColumn, [$duration['from'], $duration['to']]);
            }
        }
    }

    private function applyStatusFilter($query, Request $request, string $dateColumn = 'start_datetime'): void
    {
        if ($request->has('status') && $request->status != "") {
            if ($request->status === "upcomming") {
                $query->where($dateColumn, '>', now());
            } else {
                $query->where('booking_status', $request->status);
            }
        }
    }

    private function applySorting($query, Request $request): void
    {
        if ($request->has('sortby') && $request->sortby != "") {
            switch ($request->sortby) {
                case 'asc':
                    $query->orderBy('id', 'asc');
                    break;
                case 'desc':
                    $query->orderBy('id', 'desc');
                    break;
                case 'alphabet':
                    $query->with([
                        'vehicle' => function ($q) {
                            $q->orderBy('name', 'asc');
                        },
                    ])->orderBy(DB::raw(self::VEHICLE_NAME_SUBQUERY), 'asc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
                    break;
            }
        }
    }

    private function sendCancellationNotifications(Booking $booking): void
    {
        try {
            $authUser = Auth::user();
            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $vehicle = VehicleInfo::find($booking->vehicle_id ?? '');
            $driver = Driver::find($booking->driver_id ?? '');
            $appAdmin = \App\Models\User::where('user_type', 1)->first();

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
                'tototal_amount'  => $booking->final_price ?? "",
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
}

