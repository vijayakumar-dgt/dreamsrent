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

class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = DB::table('vehicle_info')->select('id', 'name')->where('deleted_at', null)->orderBy('name', 'asc')->get();
        $users = DB::table('users')->select('id', 'name')->orderBy('name', 'asc')->get();
        $checklists = Checklist::where('status',true)->orderBy('name','asc')->get();
        $data = [
            'cars' => $cars,
            'users' => $users,
            'checklists' => $checklists
        ];
        return view('carinfo::inspection.index', $data);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'vehicle_info_id' => 'required|exists:vehicle_info,id',
            'inspection_date' => 'required|date|after_or_equal:today',
            'inspection_by' => 'required|exists:users,id',
            'odometer'      => 'required|numeric|min:0',
            'fuel'          => 'required|numeric|min:0',
            'inspection_status' => 'required',
            'repair_status' => 'required',
        ],[
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
            ],422);
        }

        $successMessage = empty($request->id) ? __('admin.rentals.inspection_create_success') : __('admin.rentals.inspection_update_success');
        $errorMessage = empty($request->id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $successMessage = "";
            if($request->has('id') && $request->id != null){
                $inspection = Inspection::find($request->id);
            }else{
                $inspection = new Inspection();
            }
            $inspection->vehicle_info_id = $request->vehicle_info_id;
            $inspection->inspection_date = $request->inspection_date ? Carbon::parse($request->inspection_date)->format('Y-m-d') : null;
            $inspection->inspector_id = $request->inspection_by;
            $inspection->odometer = $request->odometer;
            $inspection->fuel = $request->fuel;
            $inspection->notes = $request->notes;
            $inspection->inspection_status = $request->inspection_status;
            $inspection->repair_status = $request->repair_status;
            if($request->has('checklist_id') && is_array($request->checklist_id) && count($request->checklist_id) > 0){
                $inspection->check_list = json_encode($request->checklist_id);
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

    public function getInspections(Request $request)
    {
        $inspections = Inspection::with(['car', 'inspector']);
    
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
            $inspection->inspectiondate = $inspection->inspection_date
                ? Carbon::parse($inspection->inspection_date)->format('d M Y')
                : null;

            

            return $inspection;
        });
    
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $inspections
        ]);
    }
    

    public function getInspection($id){
        try {
            $inspection = Inspection::with('car','inspector')
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

    public function deleteInspection(Request $request){
        try {
            $inspection = Inspection::findOrFail($request->delete_id);
            $inspection->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.inspection_delete_success')
            ],200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

           return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => 'Inspection not found!'
           ],422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ],422);
        }
    }

    public function getVehicles(Request $request)
    {
        $vehicles = VehicleInfo::where('status', 1)
                       ->when($request->has('search') && $request->search != null, function ($query) use ($request) {
                           $query->where('name', 'like', '%' . $request->search . '%');
                       })
                       ->get(['id', 'name'])->map(function ($vehicle) {
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

    public function checkVehicleInspection(Request $request)
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
