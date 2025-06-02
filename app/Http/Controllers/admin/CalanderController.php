<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CalendarRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalanderController extends Controller
{
    protected CalendarRepositoryInterface $calendarRepository;

    public function __construct(CalendarRepositoryInterface $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    public function index(): View
    {
        $data = $this->calendarRepository->index();
        return view('admin.calender.index', $data);
    }

    public function getCalenderBooking(Request $request): JsonResponse
    {
        $response = $this->calendarRepository->getCalenderBooking($request);
        return response()->json($response, $response['code']);
    }

    public function getBookingDetail(Request $request): JsonResponse
    {
        $bookingId = $request->get('booking_id');
        $response = $this->calendarRepository->getBookingDetail($bookingId);
        return response()->json($response, $response['code']);
    }
}
