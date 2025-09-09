<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\CylinderRequest;
use Modules\CarInfo\Repositories\Contracts\CylinderRepositoryInterface;

class CylinderController extends Controller
{
    protected CylinderRepositoryInterface $cylinderRepository;

    public function __construct(CylinderRepositoryInterface $cylinderRepository)
    {
        $this->cylinderRepository = $cylinderRepository;
    }

    public function index(): View
    {
        return view('carinfo::cylinder.index');
    }

    public function storeCylinderType(CylinderRequest $request): JsonResponse
    {
        $response = $this->cylinderRepository->storeCylinderType($request);
        return response()->json($response, $response['code']);
    }

    public function getCylinders(): JsonResponse
    {
        $response = $this->cylinderRepository->getCylinders();
        return response()->json($response, $response['code']);
    }

    public function getCylinder($id): JsonResponse
    {
        $response = $this->cylinderRepository->getCylinder($id);
        return response()->json($response, $response['code']);
    }

    public function deleteCylinder(Request $request): JsonResponse
    {
        $result = $this->cylinderRepository->deleteCylinder($request);
        return response()->json($result, $result['code']);
    }

    public function getCylinderServerside(Request $request): JsonResponse
    {
        $result = $this->cylinderRepository->getCylinderServerside($request);
        return response()->json($result);
    }
}
