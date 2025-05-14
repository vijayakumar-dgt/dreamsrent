<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\Checklist;
use Modules\CarInfo\Models\Inspection;
use Modules\CarInfo\Models\VehicleInfo;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

use function PHPUnit\Framework\isNull;

class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $cars = DB::table('vehicle_info')->select('id', 'name')->where('deleted_at', null)->orderBy('name', 'asc')->get();
        $users = DB::table('users')->select('id', 'name')->where('user_type', 2)->orderBy('name', 'asc')->get();
        $checklists = Checklist::where('status', true)->orderBy('name', 'asc')->get();
        $data = [
            'cars' => $cars,
            'users' => $users,
            'checklists' => $checklists
        ];
        return view('carinfo::inspection.index', $data);
    }

    public function save(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_info_id' => 'required|exists:vehicle_info,id',
            'inspection_date' => 'required|date|after_or_equal:today',
            'inspection_by' => 'required|exists:users,id',
            'odometer'      => 'required|numeric|min:0',
            'fuel'          => 'required|numeric|min:0',
            'inspection_status' => 'required',
            'repair_status' => 'required',
        ], [
          'vehicle_info_id.required' => __('admin.rentals.vehicle_required'),
          'inspection_date.required' => __('admin.rentals.inspection_date_required'),
          'inspection_date.date' => 'Please enter valid date',
          'inspection_date.before_or_equal' => 'Please enter future date',
          'inspection_by.required' => __('admin.rentals.inspection_by_required'),
          'inspection_by.exists' => 'Please select valid user',
          'odometer.required' => __('admin.rentals.odometer_required'),
          'odometer.numeric' => __('admin.rentals.odometer_valid'),
          'odometer.min' => __('admin.rentals.odometer_valid'),
          'fuel.required' => __('admin.rentals.fuel_required'),
          'fuel.numeric' => __('admin.rentals.fuel_valid'),
          'fuel.min' => __('admin.rentals.fuel_valid'),
          'inspection_status.required' => __('admin.rentals.inspection_status_required'),
          'repair_status.required' => __('admin.rentals.repair_status_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMessage = empty($request->id) ? __('admin.rentals.inspection_create_success') : __('admin.rentals.inspection_update_success');
        $errorMessage = empty($request->id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            if ($request->has('id') && $request->id != null) {
                /** @var \Modules\CarInfo\Models\Inspection */
                $inspection = Inspection::find($request->id);
                if ($inspection == null) {
                    return response()->json([
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Inspection not found.',
                    ], 404);
                }
            } else {
                $inspection = new Inspection();
            }
            $inspection->vehicle_info_id = $request->vehicle_info_id;
            $inspection->inspection_date = Carbon::parse($request->inspection_date)->format('Y-m-d');
            $inspection->inspector_id = $request->inspection_by;
            $inspection->odometer = $request->odometer;
            $inspection->fuel = $request->fuel;
            $inspection->notes = $request->notes;
            $inspection->inspection_status = $request->inspection_status;
            $inspection->repair_status = $request->repair_status;
            if ($request->has('checklist_id') && is_array($request->checklist_id) && count($request->checklist_id) > 0) {
                // Attempt to encode the checklist data
                $encodedChecklist = json_encode($request->checklist_id);

                // Check if json_encode was successful
                if ($encodedChecklist === false) {
                    // If encoding fails, set check_list to null (or an empty string)
                    $inspection->check_list = null;
                } else {
                    // If encoding is successful, assign the encoded data
                    $inspection->check_list = $encodedChecklist;
                }
            }

            $inspection->save();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data' => $inspection,
                'message' => $successMessage
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $errorMessage
            ], 422);
        }
    }

    public function getInspections(Request $request): JsonResponse
    {
        $inspections = Inspection::with([
                'car', 
                'inspector',
                'inspector.userDetails:id,user_id,first_name,last_name,profile_image',
            ]);

        if ($request->has('search') && $request->search != null) {
            $search = $request->search;

            $inspections = $inspections->where(function ($query) use ($search) {
                $query->whereHas('car', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('inspector', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->has('status') && $request->status != null) {
            $status = $request->status;
            $inspections = $inspections->where('inspection_status', $status);
        }

        $inspections = $inspections->orderBy('id', 'desc')->get()->map(function ($inspection) {
            $inspection->inspectiondate = formatDateTime($inspection->inspection_date, false);
            if ($inspection->inspector) {
                $inspection->inspector->name = ucwords($inspection->inspector->name); 
                if ($inspection->inspector->userDetails) {
                    $inspection->inspector->name = $inspection->inspector->userDetails->first_name 
                        ? ucwords($inspection->inspector->userDetails->first_name . ' ' . $inspection->inspector->userDetails->last_name) 
                        : ucwords($inspection->inspector->name);
                    $inspection->inspector->profile_image = uploadedAsset($inspection->inspector->userDetails->profile_image ?? null, 'profile');
                }
            }
            if ($inspection->car) {
                $inspection->car->vehicle_image = uploadedAsset($inspection->car->vehicle_image ?? null, 'default');
            }
            unset($inspection->inspector->userDetails);
            return $inspection;
        });

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $inspections
        ]);
    }


    public function getInspection(int|string $id): JsonResponse
    {
        $id = (int) $id;
        try {
            $inspection = Inspection::with('car', 'inspector')
                            ->find($id);
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data' => $inspection
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function deleteInspection(Request $request): JsonResponse
    {
        try {
            $inspection = Inspection::where('id', $request->delete_id)->firstOrFail();
            $inspection->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.inspection_delete_success')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => 'Inspection not found!'
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function getVehicles(Request $request): JsonResponse
    {
        $vehicles = VehicleInfo::where('status', 1)
                       ->when($request->has('search') && $request->search != null, function ($query) use ($request) {
                           $query->where('name', 'like', '%' . $request->search . '%');
                       })
                       ->get(['id', 'name'])
                       ->map(function (VehicleInfo $vehicle) {
                           return [
                               'id' => $vehicle->id,
                               'text' => $vehicle->name
                           ];
                       });

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $vehicles
        ]);
    }

    public function checkVehicleInspection(Request $request): JsonResponse
    {
        $inspection_date = Carbon::parse($request->inspection_date)->format('Y-m-d');
        $exists = Inspection::where('vehicle_info_id', $request->vehicle_info_id)
            ->where('inspection_date', $inspection_date)
            ->when($request->inspection_id, function ($query) use ($request) {
                $query->where('id', '!=', $request->inspection_id);
            })
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
