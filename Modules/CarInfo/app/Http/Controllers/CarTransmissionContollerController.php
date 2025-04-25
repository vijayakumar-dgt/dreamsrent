<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CarInfo\Models\Transmission;

class CarTransmissionContollerController extends Controller
{
    public function index()
    {
        return view('carinfo::car_transmission.index');
    }

    public function store(Request $request)
    {
        $id = $request->id ?? '';
        $authUser = current_user(); // Make sure this helper returns the logged-in user
        $languageId = $authUser->language_id ?? 1;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:30',
                Rule::unique('transmissions')->ignore($id)->whereNull('deleted_at')
            ],
        ], [
            'name.required' => __('admin.rentals.vehicle_transmission_required'),
            'name.unique' => __('admin.rentals.vehicle_transmission_unique'),
            'name.max' => __('admin.rentals.vehicle_transmission_maxlenght'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.rentals.vehicle_transmission_create_success') : __('admin.rentals.vehicle_transmission_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'name' => $request->name,
                'status' => $request->status ?? 1,
                'language_id' => $languageId
            ];

            if (empty($id)) {
                Transmission::create($data);
            } else {
                Transmission::where('id', $id)->update($data);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg
            ], 500);
        }
    }


    public function list(Request $request)
    {
        $orderBy = $request->order_by ?? 'desc';
        $search = $request->input('search');
        $status = $request->input('status');

        try {
            $authUser = current_user();
            $languageId = $authUser->language_id ?? 1;

            $query = Transmission::orderBy('id', $orderBy)
                ->where('language_id', $languageId);

            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%"); // Adjust 'name' if your field differs
            }

            if ($status !== null && $status !== '') {
                $query->where('status', $status); // Assumes 'status' column exists in categories table
            }

            $data = $query->get();

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

    public function edit(Request $request)
    {
        $id = $request->id;
        $transmission = Transmission::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $transmission
        ], 200);
    }

    public function delete(Request $request)
    {
        try {

            $id = $request->id;

            Transmission::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.vehicle_transmission_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return response()->json(['success' => false, 'message' => 'No items selected.']);
        }

        Transmission::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected items deleted successfully.']);
    }
}
