<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\Report\Repositories\Contracts\ReportRepositoryInterface;

class ReportController extends Controller
{
    protected ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function incomeReport(): View
    {
        $data = $this->reportRepository->incomeReport();
        return view('report::incomeReport', [...$data]);
    }

    public function earningReport(): View
    {
        $data = $this->reportRepository->earningReport();
        return view('report::earningReport', [...$data]);
    }

    public function getMonthlyEarnings(): JsonResponse
    {
        $monthlyEarnings = $this->reportRepository->getMonthlyEarnings();
        return response()->json($monthlyEarnings);
    }

    public function getEarningsBreakdown(): JsonResponse
    {
        $breakdown = $this->reportRepository->getEarningsBreakdown();
        return response()->json($breakdown);
    }

    public function fetchFilteredBookings(): JsonResponse
    {
        return response()->json([
            'message' => 'Method implementation required'
        ]);
    }
}
