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

    public function seasonalInfo(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleInfoRepository->seasonalInfo($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function tarrifInfo(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleInfoRepository->tariffInfo($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function documents(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleInfoRepository->documents($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function faq(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleInfoRepository->faq($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function damage(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleInfoRepository->damage($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function insurance(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleInfoRepository->insurance($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function getModel(Request $request): JsonResponse
    {
        $brandId = $request->brand_id;
        $response = $this->vehicleInfoRepository->getModel($brandId);
        return response()->json($response, $response['code']);
    }

    public function getTypeAndModel(Request $request): JsonResponse
    {
        $categoryId = $request->category_id;
        $response = $this->vehicleInfoRepository->getTypeAndModel($categoryId);
        return response()->json($response, $response['code']);
    }

    public function vehicleDetailsList(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->vehicleDetailsList($request);
        return response()->json($response, $response['code']);
    }

    public function deleteVehicleImage(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->deleteVehicleImage($request);
        return response()->json($response, $response['code']);
    }

    public function deleteVehiclePolicy(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->deleteVehiclePolicy($request);
        return response()->json($response, $response['code']);
    }

    public function vehicleIntrestLists(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->vehicleInterestLists($request);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $vehicleId = $request->input('delete_id');
        $response = $this->vehicleInfoRepository->delete($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function getDamageDetails(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->getDamageDetails($request);
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
