<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\CarInfo\Repositories\Contracts\VehicleManagementRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\VehicleQueryRepositoryInterface;

class VehicleInfoController extends Controller
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

    public function seasonalInfo(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleQueryRepository->seasonalInfo($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function tarrifInfo(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleQueryRepository->tariffInfo($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function documents(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleQueryRepository->documents($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function faq(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleQueryRepository->faq($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function damage(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleQueryRepository->damage($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function insurance(Request $request): JsonResponse
    {
        $vehicleId = $request->vehicle_id;
        $response = $this->vehicleQueryRepository->insurance($vehicleId);
        return response()->json($response, $response['code']);
    }

    public function getModel(Request $request): JsonResponse
    {
        $brandId = $request->brand_id;
        $response = $this->vehicleQueryRepository->getModel($brandId);
        return response()->json($response, $response['code']);
    }

    public function getTypeAndModel(Request $request): JsonResponse
    {
        $categoryId = $request->category_id;
        $response = $this->vehicleQueryRepository->getTypeAndModel($categoryId);
        return response()->json($response, $response['code']);
    }

    public function vehicleDetailsList(Request $request): JsonResponse
    {
        $response = $this->vehicleQueryRepository->vehicleDetailsList($request);
        return response()->json($response, $response['code']);
    }

    public function deleteVehicleImage(Request $request): JsonResponse
    {
        $response = $this->vehicleManagementRepository->deleteVehicleImage($request);
        return response()->json($response, $response['code']);
    }

    public function deleteVehiclePolicy(Request $request): JsonResponse
    {
        $response = $this->vehicleManagementRepository->deleteVehiclePolicy($request);
        return response()->json($response, $response['code']);
    }

    public function vehicleIntrestLists(Request $request): JsonResponse
    {
        $response = $this->vehicleQueryRepository->vehicleInterestLists($request);
        return response()->json($response, $response['code']);
    }

    public function getDamageDetails(Request $request): JsonResponse
    {
        $response = $this->vehicleQueryRepository->getDamageDetails($request);
        return response()->json($response, $response['code']);
    }
}

