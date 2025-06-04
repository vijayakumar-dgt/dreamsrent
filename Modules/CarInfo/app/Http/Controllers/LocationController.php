<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\LocationWorkingDay;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Modules\CarInfo\Http\Requests\LocationRequest;
use Modules\CarInfo\Repositories\Contracts\LocationRepositoryInterface;

class LocationController extends Controller
{
    protected LocationRepositoryInterface $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('carinfo::location.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeLocation(LocationRequest $request): JsonResponse
    {
        $response = $this->locationRepository->store($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Retrieve all locations ordered by ID in descending order.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocations(Request $request): JsonResponse
    {
        $response = $this->locationRepository->getAll($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Get a location by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocation($id): JsonResponse
    {
        $response = $this->locationRepository->getById($id);
        return response()->json($response, $response['code']);
    }

    /**
     * Delete a location by ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLocation(Request $request): JsonResponse
    {
        $id = $request->delete_id;
        $response = $this->locationRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function getCountries(Request $request): JsonResponse
    {
        $response = $this->locationRepository->getCountries($request);
        return response()->json($response, $response['code']);
    }

    public function getStates(Request $request): JsonResponse
    {
        $countryId = $request->country_id;

        $validator = Validator::make($request->all(), [
            'country_id' => [
                'required',
            ],
        ], [
            'country_id.required' => __('Country id is required.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => $validator->messages()->toArray()
            ], 422);
        }

        $response = $this->locationRepository->getStates($countryId);
        return response()->json($response, $response['code']);
    }

    public function getCities(Request $request): JsonResponse
    {
        $stateId = $request->state_id;

        $validator = Validator::make($request->all(), [
            'state_id' => [
                'required',
            ],
        ], [
            'state_id.required' => __('State id is required.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => $validator->messages()->toArray()
            ], 422);
        }

        $response = $this->locationRepository->getCities($stateId);
        return response()->json($response, $response['code']);
    }

    public function getAllLocations(Request $request): JsonResponse
    {
        $response = $this->locationRepository->getAllLocations($request);
        return response()->json($response, $response['code']);
    }
}
