<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Booking\Repositories\Contracts\BookingRepositoryInterface;
use Modules\Booking\Http\Request\BookingRequest;

class BookingController extends Controller
{
    protected BookingRepositoryInterface $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }
    public function index(): View
    {
        return view('booking::reservation.index');
    }

    public function create(): View
    {
        $data = $this->bookingRepository->create();
        return view('booking::reservation.add', $data);
    }

    public function getCustomerDetails(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->getCustomerDetails($request);
        return response()->json($response, $response['code']  ?? 200);
    }

    public function getFilterVehicles(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->getFilterVehicles($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function store(BookingRequest $request): JsonResponse
    {
        $response = $this->bookingRepository->store($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function edit(Request $request, string|int|null $id): View
    {
        $data = $this->bookingRepository->edit($request, $id);
        return view('booking::reservation.edit', [...$data]);
    }
    public function delete(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->delete($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function complete(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->complete($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function bookingList(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->bookingList($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function getBookingDetails(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->getBookingDetails($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function reservationViewDetails(Request $request, string|int|null $id): View
    {
        $data = $this->bookingRepository->reservationViewDetails($request, $id);
        return view('booking::reservation.view_details', [...$data]);
    }

    public function calculateTotalPrice(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->calculateTotalPrice($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function cancelBooking(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->cancelBooking($request);
        return response()->json($response, $response['code'] ?? 200);
    }
}
