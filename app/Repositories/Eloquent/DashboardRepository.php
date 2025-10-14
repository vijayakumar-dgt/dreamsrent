<?php

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\Maintenance;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function index(): array
    {
        $currentUser = currentUser();

        $languageId = $currentUser->language_id ?? 1;
        [$thisWeekRange, $lastWeekRange] = $this->getWeekRanges();

        $carTypes = $this->getCarTypes($languageId);
        $booking = Booking::get();
        $today = Carbon::today();

        $thisWeekCount = $this->getBookingCountForRange($thisWeekRange);
        $lastWeekCount = $this->getBookingCountForRange($lastWeekRange);
        [$percentageChange, $sign] = $this->formatChange($thisWeekCount, $lastWeekCount);

        $bookingCount = $this->getActiveBookingCount($today);

        $thisWeekAmount = $this->getBookingAmountForRange($thisWeekRange);
        $lastWeekAmount = $this->getBookingAmountForRange($lastWeekRange);
        [$amountPercentageChange, $amountSymbol] = $this->formatChange($thisWeekAmount, $lastWeekAmount);

        $amount = $this->getTotalBookingAmount();
        $symbol = $this->getCurrencySymbol();

        $thisWeekCars = $this->getCarCountForRange($thisWeekRange, $languageId);
        $lastWeekCars = $this->getCarCountForRange($lastWeekRange, $languageId);
        [$carPercentageChange, $carSymbol] = $this->formatChange($thisWeekCars, $lastWeekCars);

        $upcomingCount = Booking::whereDate('start_datetime', '>', $today)->count();

        $reservations = Booking::Join('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
            ->LeftJoin('car_fuels', 'vehicle_info.fuel_type_id', '=', 'car_fuels.id')
            ->LeftJoin('driving_types', 'vehicle_info.type_id', '=', 'driving_types.id')
            ->LeftJoin('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select(
                'bookings.*',
                'vehicle_info.name',
                'vehicle_info.vehicle_image',
                'driving_types.name as driving_name',
                'car_fuels.fuel_type',
                'user_details.profile_image',
            )
            ->where('bookings.deleted_at', null)
            ->where('booking_by', '!=', 'quotation')
            ->orderBy('bookings.id', 'desc')
            ->limit(5)
            ->get();

        // Add day count to each booking
        $reservations->transform(function ($booking) {
            /** @var \Modules\Booking\Models\Booking $booking */
            $start = Carbon::parse($booking->start_datetime);
            $end = Carbon::parse($booking->end_datetime);

            // +1 if you want to include both start and end date as full days
            $booking->day_count = (int) $start->diffInDays($end) + 1;

            return $booking;
        });

        $users = User::leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->whereNull('users.deleted_at')
            ->where('users.user_type', 3)
            ->orderByDesc('users.id')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                $user->name = empty($user->first_name)
                    ? ucwords($user->name)
                    : ucwords($user->first_name . ' ' . $user->last_name);
                $user->encrypted_id = customEncrypt($user->id, User::$userSecretKey);
                return $user;
            });

        $chartbooking = Booking::Join('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
            ->get();

        $chartbooking->groupBy(function ($booking) {
            return Carbon::parse($booking->booking_date)->format('Y-m-d'); // Group by date
        })
            ->map(function ($dayBookings) {
                $firstBooking = $dayBookings->first();

                return [
                    'date'   => $firstBooking?->booking_date,
                    'income' => $dayBookings->sum(function ($booking) {
                        return ($booking->payment_status == 1 || $booking->booking_by == 'admin') ?
                            $booking->final_price : 0;
                    }),
                    'expense' => 0
                ];
            })
            ->values();

        $maintenances = Maintenance::Join('vehicle_info', 'maintenances.vehicle_id', '=', 'vehicle_info.id')
            ->LeftJoin('car_models', 'vehicle_info.model_id', '=', 'car_models.id')
            ->where('maintenances.deleted_at', null)
            ->orderBy('maintenances.id', 'desc')
            ->limit(5)
            ->get();

        $now = Carbon::now();

        $drivers = DB::table('drivers')
            ->leftJoin('bookings', 'drivers.id', '=', 'bookings.driver_id')
            ->select(
                'drivers.id',
                'drivers.driver_name',
                'drivers.email',
                'drivers.phone_number',
                'drivers.image',
                DB::raw('COUNT(bookings.id) as total_bookings'),
                DB::raw("SUM(
            CASE
                WHEN bookings.start_datetime <= '$now' AND bookings.end_datetime >= '$now'
                THEN 1
                ELSE 0
            END
        ) as currently_in_ride")
            )
            ->groupBy('drivers.id', 'drivers.driver_name', 'drivers.email', 'drivers.phone_number', 'drivers.image')
            ->orderBy('drivers.id', 'desc')
            ->where('drivers.deleted_at', null)
            ->limit(5)
            ->get();


        [$dates, $times, $series, $formattedDates] = $this->getBookingHeatmapData();

        $invoices = $this->getRecentInvoices($currentUser?->language_id);

        return ['currentUser' => $currentUser, 'carTypes' => $carTypes, 'bookingCount' => $bookingCount, 'upcomingCount' => $upcomingCount, 'symbol' => $symbol, 'amount' => $amount, 'booking' => $booking, 'percentageChange' => $percentageChange, 'sign' => $sign, 'amountPercentageChange' => $amountPercentageChange, 'amountSymbol' => $amountSymbol, 'carSymbol' => $carSymbol, 'carPercentageChange' => $carPercentageChange, 'reservations' => $reservations, 'users' => $users, 'chartbooking' => $chartbooking, 'maintenances' => $maintenances, 'drivers' => $drivers, 'dates' => $dates, 'times' => $times, 'series' => $series, 'formattedDates' => $formattedDates, 'invoices' => $invoices];
    }

    private function getCarTypes(int $languageId)
    {
        return VehicleInfo::LeftJoin('car_fuels', 'vehicle_info.fuel_type_id', '=', 'car_fuels.id')
            ->LeftJoin('driving_types', 'vehicle_info.type_id', '=', 'driving_types.id')
            ->select('vehicle_info.*', 'driving_types.name as driving_name', 'car_fuels.fuel_type')
            ->where('vehicle_info.language_id', $languageId)
            ->orderBy('vehicle_info.id', 'desc')->get();
    }

    private function getWeekRanges(): array
    {
        $now = Carbon::now();

        return [
            [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
        ];
    }

    private function getBookingCountForRange(array $range): int
    {
        return Booking::whereBetween('booking_date', $range)->count();
    }

    private function getBookingAmountForRange(array $range): float
    {
        return Booking::whereBetween('booking_date', $range)
            ->where($this->bookingFilter())
            ->sum('final_price');
    }

    private function bookingFilter(): Closure
    {
        return function ($query) {
            $query->where(function ($q) {
                $q->where('booking_by', 'user')
                    ->where('payment_status', 1);
            })->orWhere('booking_by', 'admin');
        };
    }

    private function getTotalBookingAmount(): float
    {
        return Booking::where($this->bookingFilter())->sum('final_price');
    }

    private function getCurrencySymbol(): ?string
    {
        $generalSettings = GeneralSetting::where('group_id', 5)->where('key', 'currency')->first();

        if ($generalSettings === null) {
            return null;
        }

        return Currency::where('id', $generalSettings->value)->value('symbol');
    }

    private function getCarCountForRange(array $range, int $languageId): int
    {
        return VehicleInfo::whereBetween('created_at', $range)
            ->where('vehicle_info.language_id', $languageId)
            ->count();
    }

    private function formatChange(float $current, float $previous): array
    {
        if ($previous > 0) {
            $change = (($current - $previous) / $previous) * 100;
        } elseif ($current > 0) {
            $change = 100;
        } else {
            $change = 0;
        }

        $symbol = match (true) {
            $change > 0 => '+',
            $change < 0 => '-',
            default => '',
        };

        return [$symbol . abs(round($change, 2)) . '%', $symbol];
    }

    private function getActiveBookingCount(Carbon $today): int
    {
        return Booking::whereDate('start_datetime', '<=', $today)
            ->whereDate('end_datetime', '>=', $today)
            ->count();
    }

    private function getBookingHeatmapData(): array
    {
        $bookingsRes = Booking::selectRaw(
            'DATE(start_datetime) as date,
                 TIME_FORMAT(booking_date, "%H:00") as time,
                 COUNT(*) as count'
        )
            ->groupBy('date', 'time')
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $dates = $bookingsRes->pluck('date')->unique()->values();
        $times = $bookingsRes->pluck('time')->unique()->sort()->values();

        $series = $times->map(function ($time) use ($bookingsRes, $dates) {
            $seriesData = $dates->map(function ($date) use ($bookingsRes, $time) {
                $booking = $bookingsRes->where('date', $date)->where('time', $time)->first();

                return ['x' => $date, 'y' => $booking->count ?? 0];
            });

            return [
                'name' => $time,
                'data' => $seriesData->values()->all(),
            ];
        })->values()->all();

        $formattedDates = $dates->map(function ($date) {
            return Carbon::parse($date)->format('d M');
        })->values();

        return [$dates, $times, $series, $formattedDates];
    }

    private function getRecentInvoices(?int $languageId)
    {
        return Invoice::with('items')
            ->leftJoin('users', 'invoices.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('invoices.*', 'users.name', 'users.email', 'user_details.profile_image', 'user_details.first_name', 'user_details.last_name')
            ->whereNull('invoices.deleted_at')
            ->when($languageId, function ($query, $languageId) {
                $query->where('invoices.language_id', $languageId);
            })
            ->limit(5)
            ->get()
            ->map(function ($invoice) {
                $invoice->full_name = empty($invoice->first_name) ? '' : ucwords($invoice->first_name . ' ' . $invoice->last_name);
                return $invoice;
            });
    }
}
