<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\Cylinder;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Modules\CarInfo\Http\Requests\CylinderRequest;
use Modules\CarInfo\Repositories\Contracts\CylinderRepositoryInterface;

class CylinderController extends Controller
{
    protected CylinderRepositoryInterface $CylinderRepository;

    public function __construct(CylinderRepositoryInterface $CylinderRepository)
    {
        $this->CylinderRepository = $CylinderRepository;
    }

    public function index(): View
    {
        return view('carinfo::cylinder.index');
    }

    public function storeCylinderType(CylinderRequest $request): JsonResponse
    {
        $response = $this->CylinderRepository->storeCylinderType($request);
        return response()->json($response, $response['code']);
    }

    public function getCylinders(): JsonResponse
    {
        $response = $this->CylinderRepository->getCylinders();
        return response()->json($response, $response['code']);
    }


    public function getCylinder($id): JsonResponse
    {
        $response = $this->CylinderRepository->getCylinder($id);
        return response()->json($response, $response['code']);
    }


    public function deleteCylinder(Request $request): JsonResponse
    {
        $result = $this->CylinderRepository->deleteCylinder($request);
        return response()->json($result, $result['code']);
    }

    public function getCylinderServerside(Request $request): JsonResponse
    {
        $result = $this->CylinderRepository->getCylinderServerside($request);
        return response()->json($result);
    }
}
