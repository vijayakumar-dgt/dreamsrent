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
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): View
    {
        return view('carinfo::category.index');
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $result = $this->categoryRepository->store($request);
        return response()->json($result, $result['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $result = $this->categoryRepository->list($request);
        return response()->json($result, $result['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $result = $this->categoryRepository->edit($request);
        return response()->json($result, $result['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $result = $this->categoryRepository->delete($request);
        return response()->json($result, $result['code']);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $result = $this->categoryRepository->bulkDelete($request);
        return response()->json($result, $result['code']);
    }

    public function pdfExport(Request $request): JsonResponse|Response
    {
        return $this->categoryRepository->pdfExport($request);
    }
}
