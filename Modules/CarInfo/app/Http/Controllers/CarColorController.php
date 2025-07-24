<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\VehicleColorRequest;
use Modules\CarInfo\Repositories\Contracts\VehicleColorRepositoryInterface;

class CarColorController extends Controller
{
    protected VehicleColorRepositoryInterface $vehicleColorRepository;

    public function __construct(VehicleColorRepositoryInterface $vehicleColorRepository)
    {
        $this->vehicleColorRepository = $vehicleColorRepository;
    }

    public function index(): View
    {
        return view('carinfo::car_color.index');
    }

    public function store(VehicleColorRequest $request): JsonResponse
    {
        $response = $this->vehicleColorRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->vehicleColorRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleColorRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleColorRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function getVehicleColors(Request $request): JsonResponse
    {
        $response = $this->vehicleColorRepository->getVehicleColors($request);
        return response()->json($response, $response['code']);
    }
}
