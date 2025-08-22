<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\ExtraServiceRequest;
use Modules\CarInfo\Repositories\Contracts\ExtraServiceRepositoryInterface;

class ExtraServiceController extends Controller
{
    protected ExtraServiceRepositoryInterface $extraServiceRepository;

    public function __construct(ExtraServiceRepositoryInterface $extraServiceRepository)
    {
        $this->extraServiceRepository = $extraServiceRepository;
    }

    public function index(): View
    {
        return view('carinfo::extra_services.index');
    }

    /**
     * Save or update an extra service.
     *
     * @param \Illuminate\Http\Request $request The request object containing input data.
     * @return \Illuminate\Http\JsonResponse JSON response with a success or error message.
     */
    public function storeExtraService(ExtraServiceRequest $request): JsonResponse
    {
        $response = $this->extraServiceRepository->store($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Get a list of extra services.
     *
     * @param \Illuminate\Http\Request $request The request object containing input data.
     * @return \Illuminate\Http\JsonResponse JSON response containing the status, code, and list of extra services.
     */
    public function getExtraServices(Request $request): JsonResponse
    {
        $response = $this->extraServiceRepository->getAll($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Retrieve a specific extra service by its ID.
     *
     * @param int $id The ID of the extra service to retrieve.
     * @return \Illuminate\Http\JsonResponse JSON response containing the status, code, and extra service data.
     */
    public function getExtraService($id): JsonResponse
    {
        $response = $this->extraServiceRepository->getById($id);
        return response()->json($response, $response['code']);
    }

    /**
     * Delete an extra service.
     *
     * @param \Illuminate\Http\Request $request Contains the ID of the extra service to delete.
     * @return \Illuminate\Http\JsonResponse JSON response containing the status, code, and message.
     */
    public function deleteExtraService(Request $request): JsonResponse
    {
        $id = $request->delete_id;
        $response = $this->extraServiceRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function getVehicleExtraServices(Request $request): JsonResponse
    {
        $response = $this->extraServiceRepository->getVehicleExtraServices($request);
        return response()->json($response, $response['code']);
    }
}
