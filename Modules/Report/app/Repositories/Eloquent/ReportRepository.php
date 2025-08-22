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
    private function filterPaidBookings($bookings): \Illuminate\Support\Collection
    {
        return $bookings->filter(function ($booking) {
            if ($booking->booking_by === 'admin') {
                return is_null($booking->payment_status) || $booking->payment_status == 2;
            }
            return $booking->payment_status == 2;
        });
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange(float $current, float $previous): array
    {
        if ($previous > 0) {
            $percentageChange = (($current - $previous) / $previous) * 100;
            $sign = $percentageChange >= 0 ? '+' : '-';
        } else {
            $percentageChange = $current > 0 ? 100 : 0;
            $sign = $current > 0 ? '+' : '0';
        }
        
        return ['percentageChange' => $percentageChange, 'sign' => $sign];
    }

    /**
     * Format percentage change with sign
     */
    private function formatPercentageChange(float $percentage, string $sign): string
    {
        return number_format(abs($percentage), 2) . '%';
    }

    /**
     * Get monthly income data for comparison
     */
    private function getMonthlyIncomeData($bookings): array
    {
        $thisMonthIncome = (float) $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('final_price');
        $lastMonthIncome = (float) $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('final_price');
            
        return [
            'thisMonth' => $thisMonthIncome,
            'lastMonth' => $lastMonthIncome
        ];
    }

    /**
     * Get monthly breakdown data (insurance + extra services)
     */
    private function getMonthlyBreakdownData($bookings): array
    {
        $thisMonthInsurance = (float) $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_insurance_price');
        $thisMonthExtraService = (float) $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_extra_service_price');
        $thisMonthGrandTotal = $thisMonthInsurance + $thisMonthExtraService;

        $lastMonthInsurance = (float) $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('total_insurance_price');
        $lastMonthExtraService = (float) $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('total_extra_service_price');
        $lastMonthGrandTotal = $lastMonthInsurance + $lastMonthExtraService;

        return [
            'thisMonth' => $thisMonthGrandTotal,
            'lastMonth' => $lastMonthGrandTotal
        ];
    }
    public function incomeReport(): array
    {
        $bookings = $this->getBookingsWithVehicleInfo();
        $bookingsCount = $this->getPaginatedBookings();
        $totalIncome = $this->filterPaidBookings($bookings)->sum('final_price');
        $topEarningCar = $this->getTopEarningVehicle($bookings);
        $vehicle = VehicleInfo::find($topEarningCar);
        $vehicleInfo = VehicleInfo::where('status', 1)->where('deleted_at', null)->get();
        
        $weeklyIncomes = $this->getWeeklyIncomes();
        $weeklyChange = $this->calculatePercentageChange($weeklyIncomes['thisWeek'], $weeklyIncomes['lastWeek']);
        $symbol = getDefaultCurrencySymbol();
        
        // Chart processing is handled in the frontend
        
        return [
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
    }
    
    private function getBookingsWithVehicleInfo(): \Illuminate\Support\Collection
    {
        return Booking::Join('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')->get();
    }
    
    private function getPaginatedBookings(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Booking::Join('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
            ->orderby('bookings.id', 'desc')->paginate(10);
    }
    
    private function getTopEarningVehicle($bookings): ?int
    {
        return $bookings
            ->groupBy('vehicle_id')
            ->map(fn ($group) => $group->sum('final_price'))
            ->sortDesc()
            ->keys()
            ->first();
    }
    
    private function getWeeklyIncomes(): array
    {
        $startOfThisWeek = now()->startOfWeek();
        $endOfThisWeek = now()->endOfWeek();
        $startOfLastWeek = now()->subWeek()->startOfWeek();
        $endOfLastWeek = now()->subWeek()->endOfWeek();
        
        return [
            'thisWeek' => (float) Booking::whereBetween('booking_date', [$startOfThisWeek, $endOfThisWeek])->sum('final_price'),
            'lastWeek' => (float) Booking::whereBetween('booking_date', [$startOfLastWeek, $endOfLastWeek])->sum('final_price')
        ];
    }
    

    public function earningReport(): array
    {
        $bookings = $this->getBookingsWithUserDetails();
        $bookingCount = $this->getPaginatedBookingsWithUserDetails();
        
        $totalIncome = (float) $bookings->sum('final_price');
        $totalInsurancePrice = (float) $bookings->sum('total_insurance_price');
        $totalExtraServicePrice = (float) $bookings->sum('total_extra_service_price');
        $grandTotal = $totalInsurancePrice + $totalExtraServicePrice;

        // Monthly breakdown data
        $breakdownData = $this->getMonthlyBreakdownData($bookings);
        $breakdownChange = $this->calculatePercentageChange($breakdownData['thisMonth'], $breakdownData['lastMonth']);
        $percentageBreakChangeFormatted = $this->formatPercentageChange($breakdownChange['percentageChange'], $breakdownChange['sign']);

        // Vehicle earnings data
        $vehicleData = $this->getVehicleEarningsData($bookings);
        
        // Monthly income data
        $incomeData = $this->getMonthlyIncomeData($bookings);
        $incomeChange = $this->calculatePercentageChange($incomeData['thisMonth'], $incomeData['lastMonth']);
        $percentageChangeFormatted = $this->formatPercentageChange($incomeChange['percentageChange'], $incomeChange['sign']);

        $symbol = getDefaultCurrencySymbol();

        return [
            'symbol' => $symbol,
            'totalIncome' => $totalIncome,
            'bookings' => $bookings,
            'percentageChangeFormatted' => $percentageChangeFormatted,
            'sign' => $incomeChange['sign'],
            'vehicle' => $vehicleData['vehicle'],
            'topEarningCarTotal' => $vehicleData['topEarningCarTotal'],
            'percentageCarChangeFormatted' => $vehicleData['percentageCarChangeFormatted'],
            'signCar' => $vehicleData['signCar'],
            'grandTotal' => $grandTotal,
            'percentageBreakChangeFormatted' => $percentageBreakChangeFormatted,
            'signbreak' => $breakdownChange['sign'],
            'bookingCount' => $bookingCount
        ];
    }

    /**
     * Get bookings with user details
     */
    private function getBookingsWithUserDetails(): \Illuminate\Support\Collection
    {
        return Booking::join('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('bookings.*', 'users.id', 'users.name', 'user_details.id', 'user_details.user_id', 'user_details.profile_image', 'user_details.first_name', 'user_details.last_name')
            ->get()->map(function ($booking) {
                $booking->full_name = $booking->first_name ? ucwords($booking->first_name . ' ' . $booking->last_name) : $booking->name;
                return $booking;
            });
    }

    /**
     * Get paginated bookings with user details
     */
    private function getPaginatedBookingsWithUserDetails(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Booking::join('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('bookings.*', 'users.id', 'users.name', 'user_details.id', 'user_details.user_id', 'user_details.profile_image')
            ->paginate(10);
    }

    /**
     * Get vehicle earnings data with percentage changes
     */
    private function getVehicleEarningsData($bookings): array
    {
        // Overall top earning vehicle
        $earningsByCar = $bookings
            ->groupBy('vehicle_id')
            ->map(fn ($group) => $group->sum('final_price'))
            ->sortDesc();

        $topEarningCar = $earningsByCar->keys()->first();
        $topEarningCarTotal = $earningsByCar->first();
        $vehicle = VehicleInfo::find($topEarningCar);

        // Monthly vehicle earnings
        $thisMonthEarnings = $bookings->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('vehicle_id')
            ->map(fn ($group) => $group->sum('final_price'))
            ->sortDesc();
        $lastMonthEarnings = $bookings->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->groupBy('vehicle_id')
            ->map(fn ($group) => $group->sum('final_price'));

        $topEarningCarThisMonth = $thisMonthEarnings->keys()->first();
        $topEarningCarsTotal = $thisMonthEarnings->first() ?? 0;
        $lastMonthEarningsForCar = (float) ($lastMonthEarnings[$topEarningCarThisMonth] ?? 0);

        $carChange = $this->calculatePercentageChange($topEarningCarsTotal, $lastMonthEarningsForCar);
        $percentageCarChangeFormatted = $this->formatPercentageChange($carChange['percentageChange'], $carChange['sign']);

        return [
            'vehicle' => $vehicle,
            'topEarningCarTotal' => $topEarningCarTotal,
            'percentageCarChangeFormatted' => $percentageCarChangeFormatted,
            'signCar' => $carChange['sign']
        ];
    }

    public function getMonthlyEarnings(): \Illuminate\Support\Collection
    {
        return Booking::select(
            DB::raw('SUM(final_price) as total_income'),
            DB::raw('MONTH(created_at) as month')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function getEarningsBreakdown(): ?\Modules\Booking\Models\Booking
    {
        return Booking::select(
            DB::raw('SUM(total_insurance_price) as total_insurance_price'),
            DB::raw('SUM(total_extra_service_price) as total_extra_service_price'),
            DB::raw('SUM(vehicle_total_price) as vehicle_total_price')
        )->first();
    }
}
