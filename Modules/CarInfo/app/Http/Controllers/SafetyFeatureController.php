<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\SafetyFeatureRequest;
use Modules\CarInfo\Repositories\Contracts\SafetyFeatureRepositoryInterface;

class SafetyFeatureController extends Controller
{
    protected SafetyFeatureRepositoryInterface $SafetyFeatureRepository;

    public function __construct(SafetyFeatureRepositoryInterface $SafetyFeatureRepository)
    {
        $this->SafetyFeatureRepository = $SafetyFeatureRepository;
    }

    public function index(): View
    {
        return view('carinfo::safety_feature.index');
    }

    public function store(SafetyFeatureRequest $request): JsonResponse
    {
        $response = $this->SafetyFeatureRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->SafetyFeatureRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $response = $this->SafetyFeatureRepository->edit((int) $request->id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->SafetyFeatureRepository->delete((int) $request->id);
        return response()->json($response, $response['code']);
    }
}
