<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\CategoryRequest;
use Modules\CarInfo\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    protected CategoryRepositoryInterface $CategoryRepository;

    public function __construct(CategoryRepositoryInterface $CategoryRepository)
    {
        $this->CategoryRepository = $CategoryRepository;
    }

    public function index(): View
    {
        return view('carinfo::category.index');
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $result = $this->CategoryRepository->store($request);
        return response()->json($result, $result['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $result = $this->CategoryRepository->list($request);
        return response()->json($result, $result['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $result = $this->CategoryRepository->edit($request);
        return response()->json($result, $result['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $result = $this->CategoryRepository->delete($request);
        return response()->json($result, $result['code']);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $result = $this->CategoryRepository->bulkDelete($request);
        return response()->json($result, $result['code']);
    }

    public function pdfExport(Request $request): JsonResponse|Response
    {
        return $this->CategoryRepository->pdfExport($request);
    }
}
