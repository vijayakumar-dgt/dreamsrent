<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Transmission;
use Modules\CarInfo\Repositories\Contracts\VehicleTransmissionRepositoryInterface;

class VehicleTransmissionRepository implements VehicleTransmissionRepositoryInterface
{
    public function store(Request $request): array
    {
        $id = $request->id ?? '';
        $authUser = current_user();
        $languageId = $authUser->language_id ?? 1;

        $successMsg = empty($id) ? __('admin.rentals.vehicle_transmission_create_success') : __('admin.rentals.vehicle_transmission_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'name'        => $request->name,
                'status'      => $request->status ?? 1,
                'language_id' => $languageId
            ];

            Transmission::updateOrCreate(['id' => $id], $data);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMsg
            ];
        } catch (\Exception $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
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
            $languageId = $authUser->language_id ?? 1;

            $query = Transmission::orderBy('id', $orderBy)
                ->where('language_id', $languageId);

            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            if ($status !== null && $status !== '') {
                $query->where('status', $status);
            }

            $data = $query->get();

            return [
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $data,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function edit(int $id): array
    {
        $data = Transmission::find($id);

        if (!$data) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\Transmission $transmission */
            $transmission = Transmission::findOrFail($id);
            $transmission->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.vehicle_transmission_delete_success')
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }
}
