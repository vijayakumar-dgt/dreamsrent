<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\SeatType;
use Modules\CarInfo\Repositories\Contracts\VehicleSeatRepositoryInterface;

class VehicleSeatRepository implements VehicleSeatRepositoryInterface
{
    public function store(Request $request): array
    {
        $id = $request->id ?? '';

        $successMsg = empty($id) ? __('admin.rentals.seat_type_create_success') : __('admin.rentals.seat_type_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'seat_type' => $request->seat_type,
                'status' => $request->status ?? 1
            ];

            SeatType::updateOrCreate(['id' => $id], $data);

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

            $query = SeatType::orderBy('id', $orderBy);

            if (!empty($search)) {
                $query->where('seat_type', 'LIKE', "%{$search}%");
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
            ];
        }
    }

    public function edit(int $id): array
    {
        $data = SeatType::find($id);

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
            /** @var \Modules\CarInfo\Models\SeatType $seatType */
            $seatType = SeatType::findOrFail($id);
            $seatType->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.seat_type_delete_success')
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