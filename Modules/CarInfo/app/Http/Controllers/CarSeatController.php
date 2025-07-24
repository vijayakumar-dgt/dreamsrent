<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\VehicleSeatRequest;
use Modules\CarInfo\Repositories\Contracts\VehicleSeatRepositoryInterface;

class CarSeatController extends Controller
{
    protected VehicleSeatRepositoryInterface $vehicleSeatRepository;

    public function __construct(VehicleSeatRepositoryInterface $vehicleSeatRepository)
    {
        $this->vehicleSeatRepository = $vehicleSeatRepository;
    }

    public function index(): View
    {
        return view('carinfo::car_seat.index');
    }

    public function store(VehicleSeatRequest $request): JsonResponse
    {
        $response = $this->vehicleSeatRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->vehicleSeatRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleSeatRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleSeatRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
