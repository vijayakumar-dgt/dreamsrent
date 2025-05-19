<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\Booking\Models\Booking;
use Modules\GeneralSetting\Models\GeneralSetting;
use Carbon\Carbon;
use App\Models\Invoice;
use Modules\CarInfo\Models\Maintenance;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\GeneralSetting\Models\Currency;

class DashboardController extends Controller
{
    public function index(): View
    {
        $current_user = current_user();

        $languageId = $current_user->language_id ?? 1;
        $carTypes = VehicleInfo::Join('car_fuels', 'vehicle_info.fuel_type_id', '=', 'car_fuels.id')
            ->LeftJoin('driving_types', 'vehicle_info.type_id', '=', 'driving_types.id')
            ->select('vehicle_info.*', 'driving_types.name as driving_name', 'car_fuels.fuel_type')
            ->where('vehicle_info.language_id', $languageId)
            ->orderBy('vehicle_info.id', 'desc')->get();
        $today = Carbon::today();

        $booking = Booking::get();

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $endOfThisWeek = Carbon::now()->endOfWeek();

        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Count bookings
        $thisWeekCount = Booking::whereBetween('booking_date', [$startOfThisWeek, $endOfThisWeek])->count();
        $lastWeekCount = Booking::whereBetween('booking_date', [$startOfLastWeek, $endOfLastWeek])->count();

        // Calculate percentage change
        if ($lastWeekCount > 0) {
            $change = (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } else {
            // If last week was 0, and this week has bookings, it's 100% increase
            $change = $thisWeekCount > 0 ? 100 : 0;
        }

        // Format result with + or –
        $sign = $change > 0 ? '+' : ($change < 0 ? '-' : '');
        $percentageChange = $sign . abs(round($change, 2)) . '%';

        $bookingCount = Booking::whereDate('start_datetime', '<=', $today)
            ->whereDate('end_datetime', '>=', $today)
            ->count();

        // Reusable query logic for filtering user/admin bookings
        $filteredQuery = function ($query) {
            $query->where(function ($q) {
                $q->where('booking_by', 'user')->where('payment_status', 1);
            })->orWhere('booking_by', 'admin');
        };

        // Amount for this week
        $thisWeekAmount = Booking::whereBetween('booking_date', [$startOfThisWeek, $endOfThisWeek])
            ->where($filteredQuery)
            ->sum('final_price');

        // Amount for last week
        $lastWeekAmount = Booking::whereBetween('booking_date', [$startOfLastWeek, $endOfLastWeek])
            ->where($filteredQuery)
            ->sum('final_price');

        // Calculate percentage change
        if ($lastWeekAmount > 0) {
            $amountChange = (($thisWeekAmount - $lastWeekAmount) / $lastWeekAmount) * 100;
        } else {
            $amountChange = $thisWeekAmount > 0 ? 100 : 0;
        }

        // Format with +/– symbol
        $amountSymbol = $amountChange > 0 ? '+' : ($amountChange < 0 ? '-' : '');
        $amountPercentageChange = $amountSymbol . abs(round($amountChange, 2)) . '%';

        $amount = Booking::where(function ($query) {
            $query->where(function ($q) {
                $q->where('booking_by', 'user')
                    ->where('payment_status', 1);
            })->orWhere('booking_by', 'admin');
        })->sum('final_price');

        $generalSettings = GeneralSetting::where('group_id', 5)->where('key', 'currency')->first();
        $symbol = null;

        if ($generalSettings !== null) {
            $currency = Currency::where('id', $generalSettings->value)->select('symbol')->first();
            if ($currency !== null) {
                $symbol = $currency->symbol;
            }
        }

        // Count cars created this week
        $thisWeekCars = VehicleInfo::whereBetween('created_at', [$startOfThisWeek, $endOfThisWeek])
        ->where('vehicle_info.language_id', $languageId)->count();

        // Count cars created last week
        $lastWeekCars = VehicleInfo::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
        ->where('vehicle_info.language_id', $languageId)->count();

        // Calculate percentage change
        if ($lastWeekCars > 0) {
            $carChange = (($thisWeekCars - $lastWeekCars) / $lastWeekCars) * 100;
        } else {
            $carChange = $thisWeekCars > 0 ? 100 : 0;
        }

        // Format result with +/– symbol
        $carSymbol = $carChange > 0 ? '+' : ($carChange < 0 ? '-' : '');
        $carPercentageChange = $carSymbol . abs(round($carChange, 2)) . '%';


        $upcomingCount = Booking::whereDate('start_datetime', '>', $today)->count();

        $reservations = Booking::Join('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
        ->LeftJoin('car_fuels', 'vehicle_info.fuel_type_id', '=', 'car_fuels.id')
            ->LeftJoin('driving_types', 'vehicle_info.type_id', '=', 'driving_types.id')
            ->LeftJoin('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select(
                'bookings.*',
                'vehicle_info.*',
                'driving_types.name as driving_name',
                'car_fuels.fuel_type',
                'user_details.profile_image'
            )
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
            $days = $booking->day_count;

            return $booking;
        });

        $users = DB::table('users')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.deleted_at', null)
            ->limit(5)
            ->get()->map(function ($user) {
                $user->name = !empty($user->first_name) ? ucwords($user->first_name . ' ' . $user->last_name) : ucwords($user->name);
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
                    'date' => $firstBooking?->booking_date,
                    'income' => $dayBookings->sum(function ($booking) {
                        return ($booking->payment_status == 1 || $booking->booking_by == 'admin') ?
                         $booking->final_price : 0;
                    }),
                    'expense' => 0
                ];
            })
            ->values();

        $maintenances =  Maintenance::Join('vehicle_info', 'maintenances.vehicle_id', '=', 'vehicle_info.id')
            ->LeftJoin('car_models', 'vehicle_info.model_id', '=', 'car_models.id')
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
            ->limit(5)
            ->get();


        $bookingsRes = Booking::selectRaw(
            'DATE(start_datetime) as date,
                 TIME_FORMAT(booking_date, "%H:00") as time,
                 COUNT(*) as count'
        )
            ->groupBy('date', 'time')
            ->orderBy('date')
            ->orderBy('time')
            ->get();

            // Extract unique dates (x-axis) and times (y-axis)
            $dates = $bookingsRes->pluck('date')->unique()->values();
            $times = $bookingsRes->pluck('time')->unique()->sort()->values();

            // Format data for ApexCharts
            $series = [];
        foreach ($times as $time) {
            $seriesData = [];
            foreach ($dates as $date) {
                $count = $bookingsRes->where('date', $date)->where('time', $time)->first()->count ?? 0;
                $seriesData[] = ['x' => $date, 'y' => $count];
            }
            $series[] = [
                'name' => $time,
                'data' => $seriesData
            ];
        }
            $formattedDates = $dates->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('d M');
            })->values();

            $invoices = Invoice::with('items')
            ->leftJoin('users', 'invoices.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('invoices.*', 'users.name', 'users.email', 'user_details.profile_image', 'user_details.first_name', 'user_details.last_name')
            ->where('invoices.deleted_at', null)->limit(5)->get()->map(function ($invoice) {
                $invoice->full_name = !empty($invoice->first_name) ? ucwords($invoice->first_name . ' ' . $invoice->last_name) : '';
                return $invoice;
            });

        return view('admin.dashboard.index', compact(
            'current_user',
            'carTypes',
            'bookingCount',
            'upcomingCount',
            'symbol',
            'amount',
            'booking',
            'percentageChange',
            'sign',
            'amountPercentageChange',
            'amountSymbol',
            'carSymbol',
            'carPercentageChange',
            'reservations',
            'users',
            'chartbooking',
            'maintenances',
            'drivers',
            'dates',
            'times',
            'series',
            'formattedDates',
            'invoices'
        ));
    }
}
