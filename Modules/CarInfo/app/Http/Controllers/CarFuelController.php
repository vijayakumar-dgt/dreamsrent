<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\VehicleFuelRequest;
use Modules\CarInfo\Repositories\Contracts\VehicleFuelRepositoryInterface;

class CarFuelController extends Controller
{
    protected VehicleFuelRepositoryInterface $vehicleFuelRepository;

    public function __construct(VehicleFuelRepositoryInterface $vehicleFuelRepository)
    {
        $this->vehicleFuelRepository = $vehicleFuelRepository;
    }

    public function index(): View
    {
        return view('carinfo::car_fuel.index');
    }

    public function store(VehicleFuelRequest $request): JsonResponse
    {
        $response = $this->vehicleFuelRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->vehicleFuelRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleFuelRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->vehicleFuelRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
