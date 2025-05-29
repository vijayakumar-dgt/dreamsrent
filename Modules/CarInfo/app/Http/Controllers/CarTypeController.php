<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\Cartype;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Modules\CarInfo\Http\Requests\VehicleTypeRequest;
use Modules\CarInfo\Repositories\Contracts\VehicleTypeRepositoryInterface;

class CarTypeController extends Controller
{
    protected VehicleTypeRepositoryInterface $vehicleTypeRepository;

    public function __construct(VehicleTypeRepositoryInterface $vehicleTypeRepository)
    {
        $this->vehicleTypeRepository = $vehicleTypeRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function carTypes(): View
    {
        return view('carinfo::cartype.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function storeType(VehicleTypeRequest $request): JsonResponse
    {
        $response = $this->vehicleTypeRepository->store($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Get car type by id.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCarType($id): JsonResponse
    {
        $response = $this->vehicleTypeRepository->getById($id);
        return response()->json($response, $response['code']);
    }

    /**
     * Remove the specified car type from table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteType(Request $request): JsonResponse
    {
        $id = $request->delete_id;
        $response = $this->vehicleTypeRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function getCartypeServerside(Request $request): JsonResponse
    {
        $response = $this->vehicleTypeRepository->getAll($request);
        return response()->json($response, $response['code']);
    }

    public function getVehicleTypes(Request $request): JsonResponse
    {
        $response = $this->vehicleTypeRepository->getVehicleTypes($request);
        return response()->json($response, $response['code']);
    }
}
