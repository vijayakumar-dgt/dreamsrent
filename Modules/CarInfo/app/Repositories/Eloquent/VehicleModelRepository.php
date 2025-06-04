<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\CarModel;
use Modules\CarInfo\Repositories\Contracts\VehicleModelRepositoryInterface;

class VehicleModelRepository implements VehicleModelRepositoryInterface
{
    public function index(): array
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        $language_id = $authUser->language_id ?? 1;
        $brands = Brand::orderBy('id', 'desc')->where("language_id", $language_id)->where('status', 1)->get();

        $data = ['brands' => $brands];

        return $data;
    }

    public function store(Request $request): array
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return [
                'status' => 'error',
                'code'   => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ];
        }

        try {
            $id = $request->input('id');
            $languageId = $request->input('language_id') ?? $authUser->language_id;

            $data = [
                'model_name' => $request->input('model_name'),
                'brand_id'   => $request->input('brand_id'),
                'language_id' => $languageId,
                'status'      => $request->input('status', 1),
            ];

            CarModel::updateOrCreate(['id' => $id], $data);

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => empty($id)
                    ? __('admin.rentals.vehicle_model_create_success')
                    : __('admin.rentals.vehicle_model_update_success'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => empty($id)
                    ? __('admin.common.default_create_error')
                    : __('admin.common.default_update_error'),
            ];
        }
    }

    public function list(Request $request): array
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return [
                'status' => 'error',
                'code'   => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ];
        }
        try {
            $language_id = $authUser->language_id;
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
            $totalRecords = $query->count();
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

            return [
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data' => $data,
                'code' => 200,
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function edit(int $id): array
    {
        $data = CarModel::find($id);

        if (!$data) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        return [
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\CarModel $carModel */
            $carModel = CarModel::findOrFail($id);
            $carModel->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.vehicle_model_delete_success')
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }

    public function getVehicleModels(Request $request): array
    {
        try {
            $orderBy = $request->order_by ?? 'asc';
            $search = $request->search ?? null;

            $data = CarModel::when(function ($query) use ($search) {
                    return $query->where('model_name', 'LIKE', "%{$search}%");
            })
                ->orderBy('id', $orderBy)
                ->where('status', 1)
                ->get([
                    'id',
                    'model_name'
                ]);

            return [
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
        }
    }
}
