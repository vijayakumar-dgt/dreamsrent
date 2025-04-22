<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\CarModel;

class CarModelController extends Controller
{
    public function index(): View
    {
        $authUser = current_user();
        $language_id = $authUser->language_id;
        $brands = Brand::orderBy('id', 'desc')->where("language_id", $language_id)->where('status', 1)->get();

        return view('carinfo::car_model.index', compact('brands'));
    }

    public function store(Request $request): JsonResponse
    {
        $authUser = current_user();
        $id = $request->id ?? '';

        $data = [
            'model_name' => $request->model_name,
            'brand_id'   => $request->brand_id,
            'total_cars' => $request->total_cars,
        ];

        $validator = Validator::make($request->all(), [
            'model_name' => [
                'required',
                'max:30',
                'min:3',
                Rule::unique('car_models')->ignore($id)->whereNull('deleted_at')
            ],
            'brand_id' => 'required',
            'total_cars' => [
                'max:255',
            ]
        ], [
            'model_name.required' => __('admin.rentals.model_name_required'),
            'model_name.max' => __('admin.rentals.model_name_maxlength'),
            'model_name.min' => __('admin.rentals.model_name_minlength'),
            'model_name.unique' => __('admin.rentals.model_name_unique'),
            'total_cars.required' => __('admin.rentals.total_vehicles_required'),
            'brand_id.required' => __('admin.rentals.brand_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id)
            ? __('admin.rentals.vehicle_model_create_success')
            : __('admin.rentals.vehicle_model_update_success');

        $errorMsg = empty($id)
            ? __('admin.common.default_create_error')
            : __('admin.common.default_update_error');

        try {
            if (empty($id)) {
                // CREATE - use auth user language
                $data['language_id'] = $authUser->language_id;
                CarModel::create($data);
            } else {
                // UPDATE - use request language
                $data['status'] = $request->status ?? 1;
                $data['language_id'] = $request->language_id ?? null;
                CarModel::where('id', $id)->update($data);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function list(Request $request): JsonResponse
    {
        $authUser = current_user();
        $language_id = $authUser->language_id;
        try {
            $query = CarModel::join('brands', 'car_models.brand_id', '=', 'brands.id')
            ->where('car_models.language_id', $language_id)
            ->where('brands.language_id', $language_id);

            // Search
            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('car_models.model_name', 'like', "%{$search}%")
                        ->orWhere('brands.brand_name', 'like', "%{$search}%")
                        ->orWhere('car_models.total_cars', 'like', "%{$search}%");
                });
            }

            // Status Filter
            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('car_models.status', $status);
            }

            // Ordering
            $columnIndex = $request->order[0]['column'] ?? 1;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'model_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            // Validate column names to avoid SQL injection
            if (in_array($columnName, ['model_name', 'brand_name', 'total_cars', 'status'])) {
                $query->orderBy($columnName, $orderDir);
            } else {
                $query->orderBy('car_models.model_name', 'asc');
            }

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            // Total Records Count
            $filterTotalRecords = $query->count();
            $totalRecords = CarModel::where('car_models.language_id', $language_id)->count();
            // Data Fetch
            $data = $query
                ->skip($start)
                ->take($length)
                ->get([
                    'car_models.id',
                    'car_models.model_name',
                    'car_models.total_cars',
                    'car_models.status',
                    'brands.id as brand_id',
                    'brands.brand_name',
                ]);

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
        $data = CarModel::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {

            $id = $request->id ?? '';
            $ids = $request->ids ?? [];

            if ($request->has('ids') && !empty($ids)) {
                CarModel::whereIn('id', $ids)->delete();
            } else {
                CarModel::where('id', $id)->delete();
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.vehicle_model_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function getVehicleModels(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'asc';
        $search = $request->search ?? null;

        try {

            $data = CarModel::when(function ($query) use ($search) {
                return $query->where('model_name', 'LIKE', "%{$search}%");
            })
                ->orderBy('id', $orderBy)
                ->where('status', 1)
                ->get([
                    'id',
                    'model_name'
                ]);

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
