<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\DoorType;
use Modules\CarInfo\Repositories\Contracts\DoorTypeRepositoryInterface;

class DoorTypeRepository implements DoorTypeRepositoryInterface
{
    public function store(Request $request): array
    {
        $id = $request->id ?? '';

        $successMsg = empty($id) ? __('admin.rentals.door_type_create_success') : __('admin.rentals.door_type_update_success');
        $errorMsg = empty($id) ?  __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'door_type' => $request->input('door_type'),
                'status'    => $id ? ($request->input('status') ?? 1) : 1,
            ];

            DoorType::updateOrCreate(['id' => $id], $data);

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg,
            ];
        }
    }

    public function list(Request $request): array
    {
        try {
            $query = DoorType::query();

            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('door_type', 'like', "%{$search}%");
                });
            }

             // Status Filter
            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('door_types.status', $status);
            }

            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'door_type';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $query->orderBy($columnName, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            $filterTotalRecords = $query->count();
            $totalRecords = DoorType::count();

            $data = $query->skip($start)->take($length)->get();

            return [
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data' => $data,
                'code' => 200
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
        $data = DoorType::find($id);

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
            /** @var \Modules\CarInfo\Models\DoorType $doorType */
            $doorType = DoorType::findOrFail($id);
            $doorType->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.door_type_delete_success')
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
