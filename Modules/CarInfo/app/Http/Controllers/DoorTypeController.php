<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\DoorType;
use Illuminate\Validation\Rule;

class DoorTypeController extends Controller
{
    public function index()
    {
        return view('carinfo::door_type.index');
    }

    public function store(Request $request)
    {
        $id = $request->id ?? '';
        $data = [
            'door_type' => $request->door_type,
        ];

        $validator = Validator::make($request->all(), [
            'door_type' => [
                'required',
                'max:1',
                Rule::unique('door_types')->ignore($id)->whereNull('deleted_at')
            ],
        ], [
            'door_type.required' => __('admin.rentals.door_type_required'),
            'door_type.unique' => __('admin.rentals.door_type_unique'),
            'door_type.max' => __('admin.rentals.door_type_maxlength'),
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ],422);
        }

        $successMsg = "Door type added successfully.";
        $errorMsg = "An error occured while adding door type!";

        try {

            if (empty($id)) {
                DoorType::create($data);
            } else {
                $successMsg = "Door type updated successfully.";
                $errorMsg = "An error occured while updating door type!";
                $data['status'] = $request->status ?? 1;

                DoorType::where('id', $id)->update($data);
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
            ],500);
        }

    }

    public function list(Request $request): JsonResponse
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

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('An error occurred while retrieving!'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit(Request $request)
    {
        $id = $request->id;
        $doorType = DoorType::find($id);
        
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $doorType
        ], 200);
    }

    public function delete(Request $request)
    {
        try {

            $id = $request->id;

            DoorType::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => 'Door type deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'An error occured while deleting door type!'
            ],500);
        }
    }
}
