<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\MaintenanceRequest;
use Modules\CarInfo\Repositories\Contracts\MaintenanceRepositoryInterface;

class MaintenanceController extends Controller
{
    protected MaintenanceRepositoryInterface $maintenanceRepository;

    public function __construct(MaintenanceRepositoryInterface $maintenanceRepository)
    {
        $this->maintenanceRepository = $maintenanceRepository;
    }

    public function index(): View
    {
        $data = $this->maintenanceRepository->index();
        return view('carinfo::maintenance.index', $data);
    }

    public function store(MaintenanceRequest $request): JsonResponse
    {
        $response = $this->maintenanceRepository->store($request);
        return response()->json($response, $response['code']);   
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->maintenanceRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->maintenanceRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->maintenanceRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
