<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\GeneralSetting\Models\GeneralSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function incomeReport()
    {
        $bookings = Booking::Join('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
            ->get();
        $totalIncome = $bookings->filter(function ($booking) {
                if ($booking->booking_by === 'admin') {
                    return is_null($booking->payment_status) || $booking->payment_status == 2;
                } else {
                    return $booking->payment_status == 2;
                }
            })->sum('final_price');
        $topEarningCar = $bookings
            ->groupBy('vehicle_id')
            ->map(fn($group) => $group->sum('final_price'))
            ->sortDesc()
            ->keys()
            ->first();

        $vehicle = VehicleInfo::find($topEarningCar);
        $vehicleInfo = VehicleInfo::where('status', 1)->where('deleted_at', NULL)->get();

        $startOfThisWeek = now()->startOfWeek();
        $endOfThisWeek = now()->endOfWeek();

        $startOfLastWeek = now()->subWeek()->startOfWeek();
        $endOfLastWeek = now()->subWeek()->endOfWeek();

        $thisWeekIncome = Booking::whereBetween('booking_date', [$startOfThisWeek, $endOfThisWeek])->sum('final_price');
        $lastWeekIncome = Booking::whereBetween('booking_date', [$startOfLastWeek, $endOfLastWeek])->sum('final_price');

        if ($lastWeekIncome > 0) {
            $percentageChange = (($thisWeekIncome - $lastWeekIncome) / $lastWeekIncome) * 100;
            $sign = $percentageChange >= 0 ? '+' : '-';
        } else {
            $percentageChange = $thisWeekIncome > 0 ? 100 : 0;
            $sign = $thisWeekIncome > 0 ? '+' : '0'; // If last week was 0, show +100% increase
        }

        $generalSettings = GeneralSetting::where('group_id', 5)->where('key', 'currency')->first();

        $currency = DB::table('currencies')->where('id', $generalSettings->value)->select('symbol')->first();
        $symbol = $currency->symbol;


        $bookings->groupBy(function ($booking) {
            return Carbon::parse($booking->booking_date)->format('Y-m-d'); // Group by date
        })
            ->map(function ($dayBookings) {
                return [
                    'date' => $dayBookings->first()->booking_date,
                    'income' => $dayBookings->sum(function ($booking) {
                        return ($booking->payment_status == 1 || $booking->booking_by == 'admin') ? $booking->final_price : 0;
                    }),
                    'expense' => 0 // Placeholder, modify if you have expenses
                ];
            })
            ->values(); // Convert collection to array

        return view('report::incomeReport', compact("totalIncome", "topEarningCar", "vehicle", "percentageChange", "sign", "symbol", "bookings", "vehicleInfo"));
    }

    public function earningReport()
    {

        $bookings = Booking::Join('users', 'bookings.customer_id', '=', 'users.id')->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')->select('bookings.*', 'users.id', 'users.name', 'user_details.id', 'user_details.user_id', 'user_details.profile_image')->get();

        $totalIncome = $bookings->sum('final_price');

        $totalInsurancePrice = $bookings->sum('total_insurance_price');
        $totalExtraServicePrice = $bookings->sum('total_extra_service_price');

        // Get overall total
        $grandTotal = $totalInsurancePrice + $totalExtraServicePrice;


        // Get total sum for this month
        $thisMonthInsurance = $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_insurance_price');
        $thisMonthExtraService = $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_extra_service_price');
        $thisMonthGrandTotal = $thisMonthInsurance + $thisMonthExtraService;

        // Get total sum for last month
        $lastMonthInsurance = $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('total_insurance_price');
        $lastMonthExtraService = $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('total_extra_service_price');
        $lastMonthGrandTotal = $lastMonthInsurance + $lastMonthExtraService;

        // Calculate percentage change
        if ($lastMonthGrandTotal > 0) {
            $percentageBreakChange = (($thisMonthGrandTotal - $lastMonthGrandTotal) / $lastMonthGrandTotal) * 100;
        } else {
            $percentageBreakChange = $thisMonthGrandTotal > 0 ? 100 : 0;
        }

        // Determine class and icon
        if ($percentageBreakChange >= 0) {
            $class = "text-success";
            $icon = "ti ti-arrow-wave-right-up";
            $signbreak = "+";
        } else {
            $class = "text-danger";
            $icon = "ti ti-arrow-wave-right-down";
            $signbreak = "-";
        }

        $percentageBreakChangeFormatted = $signbreak . (abs($percentageBreakChange)) . '%';



        // Get the total earnings per vehicle
        $earningsByCar = $bookings
            ->groupBy('vehicle_id')
            ->map(fn($group) => $group->sum('final_price'))
            ->sortDesc();

        // Get the top-earning car ID
        $topEarningCar = $earningsByCar->keys()->first();

        // Get the total earnings for the top-earning car
        $topEarningCarTotal = $earningsByCar->first(); // Since it's already sorted, the first one has the highest earnings

        // Get vehicle details
        $vehicle = VehicleInfo::find($topEarningCar);

        // Get active vehicles
        $vehicleInfo = VehicleInfo::where('status', 1)->whereNull('deleted_at')->get();


        $thisMonthIncome = $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('final_price');

        $lastMonthIncome = $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('final_price');

        // Calculate percentage change
        if ($lastMonthIncome > 0) {
            $percentageChange = (($thisMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100;
        } else {
            $percentageChange = $thisMonthIncome > 0 ? 100 : 0;
        }

        // Format output with + or - sign
        $sign = $percentageChange >= 0 ? '+' : '-';
        $percentageChangeFormatted = $sign . (abs($percentageChange)) . '%';

        // Get earnings per vehicle for this month
        $thisMonthEarnings = $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('vehicle_id')
            ->map(fn($group) => $group->sum('final_price'))
            ->sortDesc();

        // Get earnings per vehicle for last month
        $lastMonthEarnings = $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->groupBy('vehicle_id')
            ->map(fn($group) => $group->sum('final_price'));

        // Get the top-earning car (from this month's earnings)
        $topEarningCar = $thisMonthEarnings->keys()->first();
        $topEarningCarsTotal = $thisMonthEarnings->first(); // Get highest earnings

        // Get last month's earnings for the top-earning car
        $lastMonthEarningsForCar = $lastMonthEarnings[$topEarningCar] ?? 0;

        // Calculate percentage change
        if ($lastMonthEarningsForCar > 0) {
            $percentageCarChange = (($topEarningCarsTotal - $lastMonthEarningsForCar) / $lastMonthEarningsForCar) * 100;
        } else {
            $percentageCarChange = $topEarningCarsTotal > 0 ? 100 : 0;
        }

        if ($percentageCarChange >= 0) {
            $class = "text-success";
            $icon = "ti ti-arrow-wave-right-up";
            $signCar = "+";
        } else {
            $class = "text-danger";
            $icon = "ti ti-arrow-wave-right-down";
            $signCar = "-";
        }

        $percentageCarChangeFormatted = $signCar . (abs($percentageCarChange)) . '%';

        $generalSettings = GeneralSetting::where('group_id', 5)->where('key', 'currency')->first();

        $currency = \DB::table('currencies')->where('id', $generalSettings->value)->select('symbol')->first();
        $symbol = $currency->symbol;


        return view('report::earningReport', compact("symbol", "bookings", "totalIncome", "percentageChangeFormatted", "sign", "vehicle", "topEarningCarTotal", "percentageCarChangeFormatted", "signCar", "grandTotal", "percentageBreakChangeFormatted", "signbreak"));
    }

    public function getMonthlyEarnings(Request $request)
{
    $monthlyEarnings = Booking::select(
        DB::raw('SUM(final_price) as total_income'),
        DB::raw('MONTH(created_at) as month')
    )
    ->groupBy('month')
    ->orderBy('month')
    ->get();

    return response()->json($monthlyEarnings); // Ensure JSON response
}

public function getEarningsBreakdown()
{
    $breakdown = Booking::select(
        DB::raw('SUM(total_insurance_price) as total_insurance_price'),
        DB::raw('SUM(total_extra_service_price) as total_extra_service_price'),
        DB::raw('SUM(vehicle_total_price) as vehicle_total_price')
    )->first();

    return response()->json($breakdown);
}
}
