<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\SeatType;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CarSeatController extends Controller
{
    public function index(): View
    {
        return view('carinfo::car_seat.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';

        $validator = Validator::make($request->all(), [
            'seat_type' => [
                'required',
                Rule::unique('seat_types')->ignore($id)->whereNull('deleted_at'),
                'not_regex:/<\/?script\b[^>]*>/i'
            ]
        ], [
            'seat_type.required' => __('admin.rentals.seat_type_required'),
            'seat_type.unique' => __('admin.rentals.seat_type_unique'),
            'seat_type.not_regex' => __('admin.common.script_tag_not_allowed'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.rentals.seat_type_create_success') : __('admin.rentals.seat_type_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'seat_type' => $request->seat_type,
                'status' => $request->status ?? 1
            ];

            if (empty($id)) {
                SeatType::create($data);
            } else {
                SeatType::where('id', $id)->update($data);
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
            $query = SeatType::orderBy('id', $orderBy);

            if (!empty($search)) {
                $query->where('seat_type', 'LIKE', "%{$search}%"); // Change 'name' to your actual searchable column
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
        $seatType = SeatType::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $seatType
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;

            SeatType::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.seat_type_delete_success')
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

        SeatType::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected items deleted successfully.']);
    }

    public function pdfExport(Request $request): Response
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        $seats = SeatType::whereIn('id', $ids)->get();

        $pdf = Pdf::loadView('carinfo::car_seat.pdf', compact('seats'));

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Car_Seats.pdf"');
    }
}
