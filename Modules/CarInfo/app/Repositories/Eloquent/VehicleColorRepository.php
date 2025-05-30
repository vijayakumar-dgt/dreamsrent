<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\CarColor;
use Modules\CarInfo\Repositories\Contracts\VehicleColorRepositoryInterface;

class VehicleColorRepository implements VehicleColorRepositoryInterface
{
    public function store(Request $request): array
    {
        $authUser = current_user();
        $languageId = $authUser->language_id ?? 1;
        $id = $request->id ?? '';

        $successMsg = empty($id) ? __('admin.rentals.vehicle_color_create_success') : __('admin.rentals.vehicle_color_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'name' => $request->name,
                'value' => $request->value,
                'status' => $request->status ?? 1,
                'language_id' => $languageId
            ];

            if (empty($id)) {
                CarColor::create($data);
            } else {
                CarColor::where('id', $id)->update($data);
            }

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ];
        } catch (\Exception $th) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg
            ];
        }
    }

    public function list(Request $request): array
    {
        try {
            $orderBy = $request->order_by ?? 'desc';
            $search = $request->input('search');
            $status = $request->input('status');
            $authUser = current_user();
            $language_id = $authUser->language_id ?? 1;

            $query = CarColor::orderBy('id', $orderBy)
                ->where('language_id', $language_id);

            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%"); // Adjust 'name' if your color field is named differently
            }

            if ($status !== null && $status !== '') {
                $query->where('status', $status); // Assumes 'status' column exists in categories table
            }

            $data = $query->get();

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

    public function edit(int $id): array
    {
        $data = CarColor::find($id);

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
            /** @var \Modules\CarInfo\Models\CarColor $carColor */
            $carColor = CarColor::findOrFail($id);
            $carColor->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.vehicle_color_delete_success')
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

    public function getVehicleColors(Request $request): array
    {
        $orderBy = $request->order_by ?? 'asc';
        $search = $request->search ?? null;

        try {
            $data = CarColor::when(function ($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            })
                ->orderBy('id', $orderBy)
                ->where('status', 1)
                ->get([
                    'id',
                    'name'
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