<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Repositories\Contracts\VehicleInfoRepositoryInterface;

class CarInfoController extends Controller
{
    protected VehicleInfoRepositoryInterface $vehicleInfoRepository;

    public function __construct(VehicleInfoRepositoryInterface $vehicleInfoRepository)
    {
        $this->vehicleInfoRepository = $vehicleInfoRepository;
    }

    public function vehiclelist(): View
    {
        $data = $this->vehicleInfoRepository->index();

        return view('carinfo::vehicle.index', $data);
    }

    public function vehicleadd(): View
    {
        $data = $this->vehicleInfoRepository->createVehicle();
        return view('carinfo::vehicle.add', $data);
    }

    public function vehicleedit(string $slug, Request $request): View
    {
        $data = $this->vehicleInfoRepository->editVehicle($slug, $request);
        return view('carinfo::vehicle.edit', $data);
    }

    public function getvehiclelist(): JsonResponse
    {
        $response = $this->vehicleInfoRepository->getVehicleList();
        return response()->json($response, $response['code']);
    }

    public function saveCarInfo(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->createVehicleInfo($request);
        return response()->json($response, $response['code']);
    }

    public function updateCarInfo(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->updateVehicleInfo($request);
        return response()->json($response, $response['code']);
    }

    public function vehicleListApi(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->adminVehicleList($request);
        return response()->json($response, $response['code']);
    }

    public function vehicleLists(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->vehicleLists($request);
        return response()->json($response, $response['code']);
    }

    public function getCarInfo(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->checkVehicle($request);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $vehicleId = $request->input('delete_id');
        $response = $this->vehicleInfoRepository->delete($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function deleteMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('delete_id', []);
        $response = $this->vehicleInfoRepository->delete($ids);
        return response()->json($response, $response['code']);
    }

    public function setPopular(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->setPopular($request);
        return response()->json($response, $response['code']);
    }

    public function setRecommended(Request $request)
    {
        $response = $this->vehicleInfoRepository->setRecommended($request);
        return response()->json($response, $response['code']);
    }

    public function setStatus(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->setStatus($request);
        return response()->json($response, $response['code']);
    }
}
