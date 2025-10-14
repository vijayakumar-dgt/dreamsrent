<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Repositories\Contracts\VehicleManagementRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleQueryRepositoryInterface;

class CarInfoController extends Controller
{
    protected VehicleManagementRepositoryInterface $vehicleManagementRepository;
    protected VehicleQueryRepositoryInterface $vehicleQueryRepository;

    public function __construct(
        VehicleManagementRepositoryInterface $vehicleManagementRepository,
        VehicleQueryRepositoryInterface $vehicleQueryRepository
    )
    {
        $this->vehicleManagementRepository = $vehicleManagementRepository;
        $this->vehicleQueryRepository = $vehicleQueryRepository;
    }

    public function vehiclelist(): View
    {
        $data = $this->vehicleQueryRepository->index();

        return view('carinfo::vehicle.index', $data);
    }

    public function vehicleadd(): View
    {
        $data = $this->vehicleManagementRepository->createVehicle();
        return view('carinfo::vehicle.add', $data);
    }

    public function vehicleedit(string $slug, Request $request): View
    {
        $data = $this->vehicleManagementRepository->editVehicle($slug, $request);
        return view('carinfo::vehicle.edit', $data);
    }

    public function getvehiclelist(): JsonResponse
    {
        $response = $this->vehicleQueryRepository->getVehicleList();
        return response()->json($response, $response['code']);
    }

    public function saveCarInfo(Request $request): JsonResponse
    {
        $response = $this->vehicleManagementRepository->createVehicleInfo($request);
        return response()->json($response, $response['code']);
    }

    public function updateCarInfo(Request $request): JsonResponse
    {
        $response = $this->vehicleManagementRepository->updateVehicleInfo($request);
        return response()->json($response, $response['code']);
    }

    public function vehicleListApi(Request $request): JsonResponse
    {
        $response = $this->vehicleQueryRepository->adminVehicleList($request);
        return response()->json($response, $response['code']);
    }

    public function vehicleLists(Request $request): JsonResponse
    {
        $response = $this->vehicleQueryRepository->vehicleLists($request);
        return response()->json($response, $response['code']);
    }

    public function getCarInfo(Request $request): JsonResponse
    {
        $response = $this->vehicleQueryRepository->checkVehicle($request);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $vehicleId = $request->input('delete_id');
        $response = $this->vehicleManagementRepository->delete($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function deleteMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('delete_id', []);
        $response = $this->vehicleManagementRepository->delete($ids);
        return response()->json($response, $response['code']);
    }

    public function setPopular(Request $request): JsonResponse
    {
        $response = $this->vehicleManagementRepository->setPopular($request);
        return response()->json($response, $response['code']);
    }

    public function setRecommended(Request $request)
    {
        $response = $this->vehicleManagementRepository->setRecommended($request);
        return response()->json($response, $response['code']);
    }

    public function setStatus(Request $request): JsonResponse
    {
        $response = $this->vehicleManagementRepository->setStatus($request);
        return response()->json($response, $response['code']);
    }
}
