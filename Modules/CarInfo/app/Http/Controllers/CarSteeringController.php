<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CarInfo\Models\CarSteering;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CarSteeringController extends Controller
{
    public function index(): View
    {
        return view('carinfo::car_steering.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? null;

        $validator = Validator::make($request->all(), [
            'steering_type' => [
                'required',
                'max:30',
                Rule::unique('car_steerings')->ignore($id)->whereNull('deleted_at')
            ],
        ], [
            'steering_type.required' => __('admin.rentals.steering_type_required'),
            'steering_type.unique' => __('admin.rentals.steering_type_unique'),
            'steering_type.max' => __('admin.rentals.steering_type_maxlength'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.rentals.steering_type_create_success') : __('admin.rentals.steering_type_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'steering_type' => $request->steering_type,
                'status' => $request->status ?? 1
            ];

            if (empty($id)) {
                CarSteering::create($data);
            } else {
                CarSteering::where('id', $id)->update($data);
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
            $query = CarSteering::orderBy('id', $orderBy);

            if (!empty($search)) {
                $query->where('steering_type', 'LIKE', "%{$search}%"); // Adjust field name as needed
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
        $carType = CarSteering::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $carType
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;

            CarSteering::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.steering_type_delete_success')
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

        CarSteering::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected items deleted successfully.']);
    }

    public function pdfExport(Request $request): JsonResponse|Response
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        $fuels = CarSteering::whereIn('id', $ids)->get();

        $pdf = Pdf::loadView('carinfo::car_fuel.fuelPdf', compact('fuels'));

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Car_Fuels.pdf"');
    }
}
