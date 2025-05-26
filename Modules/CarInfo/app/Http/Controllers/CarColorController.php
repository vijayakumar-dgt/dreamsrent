<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\CarColor;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CarColorController extends Controller
{
    public function index(): View
    {
        return view('carinfo::car_color.index');
    }

    public function store(Request $request): JsonResponse
    {
        $authUser = current_user();
        $languageId = $authUser->language_id ?? 1;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('car_colors', 'name')->ignore($request->id)->whereNull('deleted_at')->where('language_id', $languageId)
            ],
            'value' => [
                'required',
                Rule::unique('car_colors', 'value')->ignore($request->id)->whereNull('deleted_at')->where('language_id', $languageId)
            ],
        ], [
            'name.required' => __('admin.rentals.color_name_required'),
            'value.required' => __('admin.rentals.color_code_required'),
            'name.not_regex' => __('admin.common.script_tag_not_allowed'),
            'value.unique' => __('admin.rentals.color_code_unique'),
            'name.unique' => __('admin.rentals.color_name_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

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

    public function list(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'desc';
        $search = $request->input('search');
        $status = $request->input('status');

        try {
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

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $carColor = CarColor::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $carColor
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;

            CarColor::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.vehicle_color_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return response()->json(['success' => false, 'message' => 'No items selected.']);
        }

        CarColor::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected items deleted successfully.']);
    }

    public function getVehicleColors(Request $request): JsonResponse
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
