<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\CarFuel;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CarFuelController extends Controller
{
    public function index():View
    {
        return view('carinfo::car_fuel.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? null;
        $authUser = current_user();
        $languageId = $authUser->language_id ?? 1;

        $rules = [
            'fuel_type' => ['required'],
        ];

        if (empty($id)) {
            $rules['fuel_type'][] = 'unique:car_fuels,fuel_type';
        }

        $validator = Validator::make($request->all(), $rules, [
            'fuel_type.required' => __('admin.rentals.fuel_type_required'),
            'fuel_type.unique' => __('admin.rentals.fuel_type_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.rentals.fuel_type_create_success') : __('admin.rentals.fuel_type_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'fuel_type' => $request->fuel_type,
                'status' => $request->status ?? 1,
                'language_id' => $languageId
            ];

            if (empty($id)) {
                CarFuel::create($data);
            } else {
                CarFuel::where('id', $id)->update($data);
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
            $languageId = $authUser->language_id ?? 1;

            $query = CarFuel::orderBy('id', $orderBy)
                ->where('language_id', $languageId);

            if (!empty($search)) {
                $query->where('fuel_type', 'LIKE', "%{$search}%");
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
        $carType = CarFuel::find($id);

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

            CarFuel::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.fuel_type_delete_success')
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

        CarFuel::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected items deleted successfully.']);
    }

    public function pdfExport(Request $request): Response|JsonResponse
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        $fuels = CarFuel::whereIn('id', $ids)->get();

        $pdf = Pdf::loadView('carinfo::car_fuel.fuelPdf', compact('fuels'));

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Car_Fuels.pdf"');
    }
}
