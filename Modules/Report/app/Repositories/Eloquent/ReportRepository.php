<?php

namespace Modules\Report\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\Report\Repositories\Contracts\ReportRepositoryInterface;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * Filter bookings for paid status
     */
    private function filterPaidBookings($bookings)
    {
        return $bookings->filter(function ($booking) {
            if ($booking->booking_by === 'admin') {
                return is_null($booking->payment_status) || $booking->payment_status == 2;
            }
            return $booking->payment_status == 2;
        });
    }

    /**
     * Calculate weekly percentage change
     */
    private function calculateWeeklyChange(float $thisWeek, float $lastWeek): array
    {
        if ($lastWeek > 0) {
            $percentageChange = (($thisWeek - $lastWeek) / $lastWeek) * 100;
            $sign = $percentageChange >= 0 ? '+' : '-';
        } else {
            $percentageChange = $thisWeek > 0 ? 100 : 0;
            $sign = $thisWeek > 0 ? '+' : '0';
        }
        
        return ['percentageChange' => $percentageChange, 'sign' => $sign];
    }
    public function incomeReport(): array
    {
        $bookings = Booking::Join('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
        ->get();
        $bookingsCount = Booking::Join('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
        ->orderby('bookings.id', 'desc')->paginate(10);
        $totalIncome = $this->filterPaidBookings($bookings)->sum('final_price');
        $topEarningCar = $bookings
        ->groupBy('vehicle_id')
        ->map(fn ($group) => $group->sum('final_price'))
        ->sortDesc()
        ->keys()
        ->first();

        $vehicle = VehicleInfo::find($topEarningCar);
        $vehicleInfo = VehicleInfo::where('status', 1)->where('deleted_at', null)->get();

        $startOfThisWeek = now()->startOfWeek();
        $endOfThisWeek = now()->endOfWeek();

        $startOfLastWeek = now()->subWeek()->startOfWeek();
        $endOfLastWeek = now()->subWeek()->endOfWeek();

        $thisWeekIncome = Booking::whereBetween('booking_date', [$startOfThisWeek, $endOfThisWeek])->sum('final_price');
        $lastWeekIncome = Booking::whereBetween('booking_date', [$startOfLastWeek, $endOfLastWeek])->sum('final_price');

        $weeklyChange = $this->calculateWeeklyChange((float) $thisWeekIncome, (float) $lastWeekIncome);
        $symbol = getDefaultCurrencySymbol();

        $bookings->groupBy(function ($booking) {
            return Carbon::parse($booking->booking_date)->format('Y-m-d'); // Group by date
        })
        ->map(function ($dayBookings) {
            return [
                'date'   => $dayBookings->first()?->booking_date,
                'income' => $dayBookings->sum(function ($booking) {
                    return ($booking->payment_status == 1 || $booking->booking_by == 'admin') ? $booking->final_price : 0;
                }),
                'expense' => 0 // Placeholder, modify if you have expenses
            ];
        })

        ->values(); // Convert collection to array

        $data = [
            'totalIncome' => $totalIncome, 
            'topEarningCar' => $topEarningCar, 
            'vehicle' => $vehicle, 
            'percentageChange' => $weeklyChange['percentageChange'], 
            'sign' => $weeklyChange['sign'], 
            'symbol' => $symbol, 
            'bookings' => $bookings, 
            'vehicleInfo' => $vehicleInfo, 
            'bookingsCount' => $bookingsCount
        ];
        return $data;
    }

    public function earningReport(): array
    {
        $bookings = Booking::join('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('bookings.*', 'users.id', 'users.name', 'user_details.id', 'user_details.user_id', 'user_details.profile_image', 'user_details.first_name', 'user_details.last_name')
            ->get()->map(function ($booking) {
                $booking->full_name = $booking->first_name ? ucwords($booking->first_name . ' ' . $booking->last_name) : $booking->name;
                return $booking;
            });

        $bookingCount = Booking::join('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('bookings.*', 'users.id', 'users.name', 'user_details.id', 'user_details.user_id', 'user_details.profile_image')
            ->paginate(10);

        $totalIncome = (float) $bookings->sum('final_price');
        $totalInsurancePrice = (float) $bookings->sum('total_insurance_price');
        $totalExtraServicePrice = (float) $bookings->sum('total_extra_service_price');

        $grandTotal = $totalInsurancePrice + $totalExtraServicePrice;

        // This month
        $thisMonthInsurance = (float) $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_insurance_price');
        $thisMonthExtraService = (float) $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_extra_service_price');
        $thisMonthGrandTotal = $thisMonthInsurance + $thisMonthExtraService;

        // Last month
        $lastMonthInsurance = (float) $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('total_insurance_price');
        $lastMonthExtraService = (float) $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('total_extra_service_price');
        $lastMonthGrandTotal = $lastMonthInsurance + $lastMonthExtraService;

        // Break percentage
        $percentageBreakChange = $lastMonthGrandTotal > 0
            ? (($thisMonthGrandTotal - $lastMonthGrandTotal) / $lastMonthGrandTotal) * 100
            : ($thisMonthGrandTotal > 0 ? 100 : 0);

        $signbreak = $percentageBreakChange >= 0 ? '+' : '-';
        $percentageBreakChangeFormatted = $signbreak . abs($percentageBreakChange) . '%';
        $percentageBreakChangeFormatted = number_format((float) $percentageBreakChangeFormatted, 2);


        // Earnings per vehicle
        $earningsByCar = $bookings
            ->groupBy('vehicle_id')
            ->map(fn ($group) => $group->sum('final_price'))
            ->sortDesc();

        $topEarningCar = $earningsByCar->keys()->first();
        $topEarningCarTotal = $earningsByCar->first();

        $vehicle = VehicleInfo::find($topEarningCar);
        $vehicleInfo = VehicleInfo::where('status', 1)->whereNull('deleted_at')->get();

        $thisMonthIncome = (float) $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('final_price');
        $lastMonthIncome = (float) $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('final_price');

        $percentageChange = $lastMonthIncome > 0
            ? (($thisMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100
            : ($thisMonthIncome > 0 ? 100 : 0);
        $sign = $percentageChange >= 0 ? '+' : '-';
        $percentageChangeFormatted = $sign . abs($percentageChange) . '%';
        $percentageChangeFormatted = number_format((float) $percentageChangeFormatted, 2);
        // Per-vehicle earnings
        $thisMonthEarnings = $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('vehicle_id')
            ->map(fn ($group) => $group->sum('final_price'))
            ->sortDesc();
        $lastMonthEarnings = $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->groupBy('vehicle_id')
            ->map(fn ($group) => $group->sum('final_price'));

        $topEarningCarThisMonth = $thisMonthEarnings->keys()->first();
        $topEarningCarsTotal = $thisMonthEarnings->first();
        $lastMonthEarningsForCar = (float) ($lastMonthEarnings[$topEarningCarThisMonth] ?? 0);

        $percentageCarChange = $lastMonthEarningsForCar > 0
            ? (($topEarningCarsTotal - $lastMonthEarningsForCar) / $lastMonthEarningsForCar) * 100
            : ($topEarningCarsTotal > 0 ? 100 : 0);

        $signCar = $percentageCarChange >= 0 ? '+' : '-';
        $percentageCarChangeFormatted = $signCar . abs($percentageCarChange) . '%';
        $percentageCarChangeFormatted = number_format((float) $percentageCarChangeFormatted, 2);

        $symbol = getDefaultCurrencySymbol();

        $data = ['symbol' => $symbol, 'totalIncome' => $totalIncome, 'bookings' => $bookings, 'percentageChangeFormatted' => $percentageChangeFormatted, 'sign' => $sign, 'vehicle' => $vehicle, 'topEarningCarTotal' => $topEarningCarTotal, 'percentageCarChangeFormatted' => $percentageCarChangeFormatted, 'signCar' => $signCar, 'grandTotal' => $grandTotal, 'percentageBreakChangeFormatted' => $percentageBreakChangeFormatted, 'signbreak' => $signbreak, 'bookingCount' => $bookingCount];
        return $data;
    }

    public function getMonthlyEarnings()
    {
        $monthlyEarnings = Booking::select(
            DB::raw('SUM(final_price) as total_income'),
            DB::raw('MONTH(created_at) as month')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $monthlyEarnings;
    }

    public function getEarningsBreakdown()
    {
        $breakdown = Booking::select(
            DB::raw('SUM(total_insurance_price) as total_insurance_price'),
            DB::raw('SUM(total_extra_service_price) as total_extra_service_price'),
            DB::raw('SUM(vehicle_total_price) as vehicle_total_price')
        )->first();

        return $breakdown;
    }
}
