<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\DoorType;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\CarInfo\Http\Requests\DoorTypeRequest;
use Modules\CarInfo\Repositories\Contracts\DoorTypeRepositoryInterface;

class DoorTypeController extends Controller
{
    protected DoorTypeRepositoryInterface $doorTypeRepository;

    public function __construct(DoorTypeRepositoryInterface $doorTypeRepository)
    {
        $this->doorTypeRepository = $doorTypeRepository;
    }

    public function index(): View
    {
        return view('carinfo::door_type.index');
    }

    public function store(DoorTypeRequest $request): JsonResponse
    {
        $response = $this->doorTypeRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->doorTypeRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->doorTypeRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->doorTypeRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
