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
use Modules\CarInfo\Repositories\BrandRepository;

class BrandController extends Controller
{
    protected BrandRepository $brandRepo;

    public function __construct(BrandRepository $brandRepo)
    {
        $this->brandRepo = $brandRepo;
    }
    
    public function index(): View
    {
        return view('carinfo::brand.index');
    }

    public function store(BrandRequest $request)
    {
        $response = $this->brandRepo->store($request);
        return response()->json($response);
    }

    public function list(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User|null $authUser */
            $authUser = current_user();
            if (!$authUser) {
                return response()->json([
                    'status' => 'error',
                    'code'   => 401,
                    'message' => 'Unauthorized: User not authenticated.'
                ], 401);
            }
            $language_id = $authUser->language_id;
            $query = Brand::query()->where("language_id", $language_id);

            // Search
            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('brand_name', 'like', "%{$search}%")
                        ->orWhere('total_cars', 'like', "%{$search}%");
                });
            }

            // Status Filter
            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('brands.status', $status);
            }

            // Ordering
            $columnIndex = $request->order[0]['column'] ?? 1;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'brand_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            // Validate column names to avoid SQL injection
            if (in_array($columnName, ['brand_name', 'total_cars', 'status'])) {
                $query->orderBy($columnName, $orderDir);
            }

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            // Total Records Count
            $filterTotalRecords = $query->count();
            $totalRecords = Brand::where("language_id", $language_id)->count();

            // Data Fetch
            $data = $query->skip($start)->take($length)->get()->map(function ($brand) {
                $brand->brand_image = uploadedAsset($brand->brand_image);
                $brand->brand_icon = uploadedAsset($brand->brand_icon);
                return $brand;
            });

            // Return Response
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = Brand::where("id", $id)->first();
        if ($data) {
            $data->brand_image = uploadedAsset($data->brand_image);
            $data->brand_icon = uploadedAsset($data->brand_icon);
        }

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            Brand::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.brand_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function getBrands(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'desc';
        $search = $request->search ?? null;

        try {
            $data = Brand::when($search, function ($query) use ($search) {
                    return $query->where('brand_name', 'LIKE', "%{$search}%");
            })
                ->orderBy('id', $orderBy)
                ->where('status', 1)
                ->get(['id', 'brand_name'])
                ->map(function ($brand) {
                    $imagePath = public_path("storage/brands/{$brand->brand_image}");
                    $brand->brand_image = file_exists($imagePath) ? url("storage/brands/{$brand->brand_image}") : null;
                    return $brand;
                });

            return response()->json([
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
