<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\DamageTypeRequest;
use Modules\CarInfo\Repositories\Contracts\DamageTypeRepositoryInterface;

class DamageTypeController extends Controller
{
    protected DamageTypeRepositoryInterface $damageTypeRepository;

    public function __construct(DamageTypeRepositoryInterface $damageTypeRepository)
    {
        $this->damageTypeRepository = $damageTypeRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('carinfo::damage_type.index');
    }

    public function storeDamageType(DamageTypeRequest $request): JsonResponse
    {
        $response = $this->damageTypeRepository->store($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Get all damage types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDamageTypes(Request $request): JsonResponse
    {
        $response = $this->damageTypeRepository->getAll($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Get damage type by id
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDamageType($id): JsonResponse
    {
        $response = $this->damageTypeRepository->getById($id);
        return response()->json($response, $response['code']);
    }

    /**
     * Delete a damage type by id
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDamageType(Request $request): JsonResponse
    {
        $id = $request->delete_id;
        $response = $this->damageTypeRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
