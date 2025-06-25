<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\VehicleTransmissionRequest;
use Modules\CarInfo\Repositories\Contracts\VehicleTransmissionRepositoryInterface;

class CarTransmissionContollerController extends Controller
{
    protected VehicleTransmissionRepositoryInterface $vehicleTransmissionRepository;

    public function __construct(VehicleTransmissionRepositoryInterface $vehicleTransmissionRepository)
    {
        $this->vehicleTransmissionRepository = $vehicleTransmissionRepository;
    }

    public function index(): View
    {
        return view('carinfo::car_transmission.index');
    }

    public function store(VehicleTransmissionRequest $request): JsonResponse
    {
        $response = $this->vehicleTransmissionRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->vehicleTransmissionRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleTransmissionRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleTransmissionRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
