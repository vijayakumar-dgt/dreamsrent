<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\VehicleModelRequest;
use Modules\CarInfo\Repositories\Contracts\VehicleModelRepositoryInterface;

class CarModelController extends Controller
{
    protected VehicleModelRepositoryInterface $vehicleModelRepository;

    public function __construct(VehicleModelRepositoryInterface $vehicleModelRepository)
    {
        $this->vehicleModelRepository = $vehicleModelRepository;
    }

    public function index(): View
    {
        $data = $this->vehicleModelRepository->index();
        return view('carinfo::car_model.index', $data);
    }

    public function store(VehicleModelRequest $request): JsonResponse
    {
        $response = $this->vehicleModelRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->vehicleModelRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleModelRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleModelRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function getVehicleModels(Request $request): JsonResponse
    {
        $response = $this->vehicleModelRepository->getVehicleModels($request);
        return response()->json($response, $response['code']);
    }
}
