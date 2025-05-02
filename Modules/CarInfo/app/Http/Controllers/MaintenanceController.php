<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\CarInfo\Models\Maintenance;
use Modules\CarInfo\Models\VehicleInfo;


class MaintenanceController extends Controller
{
    public function index(): View
    {
        $vehicles = VehicleInfo::where('status', 1)->get(['id', 'name']);
        return view('carinfo::maintenance.index', compact('vehicles'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';
        $startDate = $request->start_date 
            ? Carbon::createFromFormat('d-m-Y', $request->start_date) 
            : null;
        $endDate = $request->end_date 
            ? Carbon::createFromFormat('d-m-Y', $request->end_date) 
            : null;
        
        $request->merge([
            'start_date' => $startDate ? $startDate->format('Y-m-d') : null,
            'end_date'   => $endDate ? $endDate->format('Y-m-d') : null,
        ]);
        $validator = Validator::make($request->all(), [
            'vehicle_id' => [
                'required',
            ],
            'odometer' => [
                'required',
            ],
            'start_date' => [
                'required',
            ],
            'end_date' => [
                'required',
            ],
            'details' => [
                'required',
            ],
            'status' => [
                'required',
            ]
        ], [
            'vehicle_id.required' => __('admin.rentals.vehicle_required'),
            'odometer.required' => __('admin.rentals.odometer_required'),
            'start_date.required' => __("admin.rentals.start_date_required"),
            'end_date.required' => __("admin.rentals.end_date_required"),
            'details.required' => __("admin.rentals.details_required"),
            'status.required' => __("admin.rentals.status_required"),
        ]);

        $validator->after(function ($validator) use ($request, $id) {
            $vehicleId = $request->vehicle_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $query = Maintenance::where('vehicle_id', $vehicleId)
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                });
            if (!empty($id)) {
                $query->where('id', '!=', $id);
            }
            if ($query->exists()) {
                $validator->errors()->add('start_date', __('admin.rentals.date_overlap'));
                $validator->errors()->add('end_date', __('admin.rentals.date_overlap'));
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __("admin.rentals.maintenance_create_success") : __("admin.rentals.maintenance_update_success");
        $errorMsg = empty($id) ? __("admin.rentals.maintenance_create_error") : __("admin.rentals.maintenance_update_error");

        try {
            $data = [
                'vehicle_id' => $request->vehicle_id,
                'odometer' => $request->odometer,
                'details' => $request->details,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ];

            if (empty($id)) {
                Maintenance::create($data);
            } else {
                Maintenance::where('id', $id)->update($data);
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
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $query = Maintenance::join('vehicle_info as v', 'maintenances.vehicle_id', '=', 'v.id')
                ->join('cartypes as ct', 'v.type_id', '=', 'ct.id')
                ->select([
                    'maintenances.id',
                    'maintenances.odometer',
                    'maintenances.start_date',
                    'maintenances.end_date',
                    'maintenances.details',
                    'maintenances.status',
                    'v.name as vehicle_name',
                    'ct.name as vehicle_type',
                    'v.vehicle_image',
                    'maintenances.created_at',
                ]);

            // DataTables Search
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('v.name', 'LIKE', "%{$search}%")
                        ->orWhere('ct.name', 'LIKE', "%{$search}%")
                        ->orWhere('maintenances.details', 'LIKE', "%{$search}%")
                        ->orWhere('maintenances.status', 'LIKE', "%{$search}%");
                });
            }

            // Status Filter
            if ($request->has('status') && !empty($request->status)) {
                $query->whereIn('maintenances.status', $request->status);
            }

            // Apply Date Filter
            if ($request->has('sort_by_date') && !empty($request->sort_by_date)) {
                $dates = explode(' - ', $request->sort_by_date);
                if (count($dates) === 2) {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]));
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]));

                    // Apply date filter only if valid date format
                    if ($startDate && $endDate) {
                        $query->whereBetween('maintenances.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
                    }
                }
            }

            // Apply Sort Filter
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch (strtolower($request->sort_by)) {
                    case 'latest':
                        $query->orderBy('maintenances.created_at', 'desc');
                        break;
                    case 'ascending':
                        $query->orderBy('maintenances.created_at', 'asc');
                        break;
                    case 'descending':
                        $query->orderBy('maintenances.created_at', 'desc');
                        break;
                    case 'last month':
                        $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                        $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                        $query->whereBetween('maintenances.created_at', [$startDate, $endDate]);
                        break;
                    case 'last 7 days':
                        $startDate = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                        $endDate = \Carbon\Carbon::now()->endOfDay();
                        $query->whereBetween('maintenances.created_at', [$startDate, $endDate]);
                        break;
                }
            }

            // Sorting
            $columnIndex = $request->order[0]['column'] ?? 1;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'vehicle_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';
            $query->orderBy($columnName, $orderDir);

            // Pagination
            $totalRecords = Maintenance::count();
            $filteredRecords = $query->count();

            $query->offset($request->start)->limit($request->length);
            $data = $query->get();

            // Format Response Data
            $data->map(function ($maintenance) {
                $maintenance->start_date = formatDateTime($maintenance->start_date, false);
                $maintenance->end_date = formatDateTime($maintenance->end_date, false);
                $vehicleImage = uploadedAsset(is_array($maintenance->vehicle_image) ? null : $maintenance->vehicle_image);
                $maintenance->vehicle_image = is_array($vehicleImage) ? $vehicleImage['url'] : $vehicleImage;
                $maintenance->odometer = number_format((float)$maintenance->odometer, 0, ',');

                $statusMap = [
                    Maintenance::$planned => __('admin.common.planned'),
                    Maintenance::$inprogress => __('admin.common.in_progress'),
                    Maintenance::$completed => __('admin.common.completed'),
                ];
                $maintenance->status_text = $statusMap[$maintenance->status] ?? 'Unknown';

                return $maintenance;
            });

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $data,
            ]);
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
        /** @var \Modules\CarInfo\Models\Maintenance|null $data */
        $data = Maintenance::find($id);
        if ($data) {
            $startDate = $data->start_date ? Carbon::createFromFormat('Y-m-d', $data->start_date) : null;
            $endDate = $data->end_date ? Carbon::createFromFormat('Y-m-d', $data->end_date) : null;
            $data->start_date = $startDate ? $startDate->format('d-m-Y') : null;
            $data->end_date = $endDate ? $endDate->format('d-m-Y') : null;
        }

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            Maintenance::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.maintenance_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }
}
