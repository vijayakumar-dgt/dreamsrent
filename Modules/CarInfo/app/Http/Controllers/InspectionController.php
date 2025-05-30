<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Modules\CarInfo\Http\Requests\InspectionRequest;
use Modules\CarInfo\Repositories\Contracts\InspectionRepositoryInterface;

class InspectionController extends Controller
{
    protected InspectionRepositoryInterface $inspectionRepository;

    public function __construct(InspectionRepositoryInterface $inspectionRepository)
    {
        $this->inspectionRepository = $inspectionRepository;
    }

    public function index(): View
    {
        $data = $this->inspectionRepository->index();
        return view('carinfo::inspection.index', $data);
    }

    public function save(InspectionRequest $request): JsonResponse
    {
        $response = $this->inspectionRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function getInspections(Request $request): JsonResponse
    {
        $response = $this->inspectionRepository->getAll($request);
        return response()->json($response, $response['code']);
    }

    public function getInspection(int $id): JsonResponse
    {
        $response = $this->inspectionRepository->getById($id);
        return response()->json($response, $response['code']);
    }

    public function deleteInspection(Request $request): JsonResponse
    {
        $id = $request->delete_id;
        $response = $this->inspectionRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function getVehicles(Request $request): JsonResponse
    {
        $response = $this->inspectionRepository->getVehicles($request);
        return response()->json($response, $response['code']);
    }

    public function checkVehicleInspection(Request $request): JsonResponse
    {
        $response = $this->inspectionRepository->checkVehicleInspection($request);
        return response()->json($response);
    }
}
