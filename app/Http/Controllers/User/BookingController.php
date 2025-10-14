<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\UserBookings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class BookingController extends BaseUserController
{
    public function index(): View
    {
        $data = $this->bookingRepository->getUserBookings();

        return view('frontend.user.bookings', $data);
    }

    public function last(Request $request): AnonymousResourceCollection
    {
        $bookings = $this->bookingRepository->getAjaxLastBookings($request);

        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }

    public function list(Request $request): AnonymousResourceCollection
    {
        $bookings = $this->bookingRepository->getAjaxBookings($request);

        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }

    public function show(?int $id): JsonResponse
    {
        $booking = $this->bookingRepository->getBookingDetails($id);

        return response()->json([
            'status' => 'success',
            'data'   => new UserBookings($booking),
        ]);
    }

    public function cancel(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->cancelBooking($request);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function complete(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->completeBooking($request);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function start(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->startRide($request);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function destroy(Request $request): JsonResponse
    {
        $response = $this->bookingRepository->deleteRide($request);

        return response()->json($response, $response['code'] ?? 200);
    }
}
