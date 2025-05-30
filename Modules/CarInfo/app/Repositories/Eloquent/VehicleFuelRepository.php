<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\CarFuel;
use Modules\CarInfo\Repositories\Contracts\VehicleFuelRepositoryInterface;

class VehicleFuelRepository implements VehicleFuelRepositoryInterface
{
    public function store(Request $request): array
    {
        try {
            $id = $request->id ?? null;
            $authUser = current_user();
            $languageId = $authUser->language_id ?? 1;

            $data = [
                'fuel_type' => $request->fuel_type,
                'status' => $request->status ?? 1,
                'language_id' => $languageId
            ];

            CarFuel::updateOrCreate(['id' => $id], $data);

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => empty($id) 
                    ? __('admin.rentals.fuel_type_create_success') 
                    : __('admin.rentals.fuel_type_update_success'),
            ];
        } catch (\Exception $th) {
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
        try {
            $orderBy = $request->order_by ?? 'desc';
            $search = $request->input('search');
            $status = $request->input('status');
            $authUser = current_user();
            $languageId = $authUser->language_id ?? 1;

            $query = CarFuel::orderBy('id', $orderBy)
                ->where('language_id', $languageId);

            if (!empty($search)) {
                $query->where('fuel_type', 'LIKE', "%{$search}%");
            }

            if ($status !== null && $status !== '') {
                $query->where('status', $status);
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
        $data = CarFuel::find($id);

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
            /** @var \Modules\CarInfo\Models\CarFuel $carFuel */
            $carFuel = CarFuel::findOrFail($id);
            $carFuel->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.fuel_type_delete_success')
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
}