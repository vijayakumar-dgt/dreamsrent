<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\CarInfo\Models\Brand;
use Illuminate\Http\UploadedFile;
use Modules\CarInfo\Http\Requests\BrandRequest;
use Modules\CarInfo\Repositories\Contracts\BrandRepositoryInterface;

class BrandController extends Controller
{
    protected BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }
    
    public function index(): View
    {
        return view('carinfo::brand.index');
    }

    public function store(BrandRequest $request)
    {
        $response = $this->brandRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->brandRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->brandRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->brandRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function getBrands(Request $request): JsonResponse
    {
        $response = $this->brandRepository->getBrands($request);
        return response()->json($response, $response['code']);
    }
}
