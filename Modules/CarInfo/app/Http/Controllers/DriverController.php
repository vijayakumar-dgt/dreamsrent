<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\DriverRequest;
use Modules\CarInfo\Repositories\Contracts\DriverRepositoryInterface;

class DriverController extends Controller
{
    protected DriverRepositoryInterface $driverRepository;

    public function __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    public function index(): View
    {
        $data = $this->driverRepository->index();
        return view('carinfo::driver.index', $data);
    }

    public function store(DriverRequest $request): JsonResponse
    {
        $response = $this->driverRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->driverRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = $this->driverRepository->getById($id);
        return response()->json($data, $data['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->driverRepository->delete($request);
        return response()->json($response, $response['code']);
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $response = $this->driverRepository->changeStatus($request);
        return response()->json($response, $response['code']);
    }

    public function getDrivers(Request $request): JsonResponse
    {
        $response = $this->driverRepository->getDrivers($request);
        return response()->json($response, $response['code']);
    }

    public function getDriverDetails(Request $request): JsonResponse
    {
        $driverId = $request->driver_id;
        $response = $this->driverRepository->getDriverDetails($driverId);
        return response()->json($response, $response['code']);
    }
}
