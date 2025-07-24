<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Booking\Http\Request\QuotationRequest;
use Modules\Booking\Repositories\Contracts\QuotationRepositoryInterface;

class QuotationController extends Controller
{
    protected QuotationRepositoryInterface $quotationRepository;

    public function __construct(QuotationRepositoryInterface $quotationRepository)
    {
        $this->quotationRepository = $quotationRepository;
    }

    public function index(): View
    {
        return view('booking::quotations.index');
    }

    public function create(): View
    {
        $data = $this->quotationRepository->create();
        return view('booking::quotations.add', [...$data]);
    }

    public function store(QuotationRequest $request): JsonResponse
    {
        $response = $this->quotationRepository->store($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function edit(Request $request, string|int|null $id): View
    {
        $data = $this->quotationRepository->edit($request, $id);
        return view('booking::quotations.edit', [...$data]);
    }

    public function bookingList(Request $request): JsonResponse
    {
        $response = $this->quotationRepository->bookingList($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function getBookingDetails(Request $request): JsonResponse
    {
        $response = $this->quotationRepository->getBookingDetails($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function reservationViewDetails(Request $request, string|int|null $id): View
    {
        $data = $this->quotationRepository->reservationViewDetails($request, $id);
        return view('booking::quotations.view_details', [...$data]);
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->quotationRepository->delete($request);
        return response()->json($response, $response['code'] ?? 200);
    }
}
