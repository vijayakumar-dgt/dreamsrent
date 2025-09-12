<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\CarInfo\Repositories\Contracts\VehicleInfoRepositoryInterface;

class VehicleInfoController extends Controller
{
    protected VehicleInfoRepositoryInterface $vehicleInfoRepository;

    public function __construct(VehicleInfoRepositoryInterface $vehicleInfoRepository)
    {
        $this->vehicleInfoRepository = $vehicleInfoRepository;
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

    public function getDamageDetails(Request $request): JsonResponse
    {
        $response = $this->vehicleInfoRepository->getDamageDetails($request);
        return response()->json($response, $response['code']);
    }
}

