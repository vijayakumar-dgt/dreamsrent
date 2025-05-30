<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\CarInfo\Http\Requests\VehicleSteeringRequest;
use Modules\CarInfo\Repositories\Contracts\VehicleSteeringRepositoryInterface;

class CarSteeringController extends Controller
{
    protected VehicleSteeringRepositoryInterface $vehicleSteeringRepository;

    public function __construct(VehicleSteeringRepositoryInterface $vehicleSteeringRepository)
    {
        $this->vehicleSteeringRepository = $vehicleSteeringRepository;
    }

    public function index(): View
    {
        return view('carinfo::car_steering.index');
    }

    public function store(VehicleSteeringRequest $request): JsonResponse
    {
        $response = $this->vehicleSteeringRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->vehicleSteeringRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleSteeringRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleSteeringRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
