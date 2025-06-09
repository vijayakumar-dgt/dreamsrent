<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\CarInfo\Models\Maintenance;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Repositories\Contracts\MaintenanceRepositoryInterface;

class MaintenanceRepository implements MaintenanceRepositoryInterface
{
    public function index(): array
    {
        $vehicles = VehicleInfo::where('status', 1)->get(['id', 'name']);
        $data = [
            'vehicles' => $vehicles
        ];
        return $data;
    }

    public function store(Request $request): array
    {
        $id = $request->id ?? '';

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

            Maintenance::updateOrCreate(['id' => $id], $data);

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
                'error' => $e->getMessage()
            ];
        }
    }

    public function list(Request $request): array
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
                        $query->orderBy('v.name', 'asc');
                        break;
                    case 'descending':
                        $query->orderBy('v.name', 'desc');
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
                // $maintenance->vehicle_image = uploadedAsset($maintenance->vehicle_image ?? null);

                $filename = basename($maintenance->vehicle_image ?? '');
                $vehicleImgae = 'vehicles/images/small/' . $filename;
                $maintenance->vehicle_image = uploadedAsset($vehicleImgae);
                $maintenance->odometer = number_format((float)$maintenance->odometer, 0, ',');

                $statusMap = [
                    Maintenance::$planned => __('admin.common.planned'),
                    Maintenance::$inprogress => __('admin.common.in_progress'),
                    Maintenance::$completed => __('admin.common.completed'),
                ];
                $maintenance->status_text = $statusMap[$maintenance->status] ?? 'Unknown';

                return $maintenance;
            });

            return [
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $data,
                'code' => 200
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function edit(int $id): array
    {
        $data = Maintenance::find($id);

        if (!$data) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        $startDate = $data->start_date ? Carbon::createFromFormat('Y-m-d', $data->start_date) : null;
        $endDate = $data->end_date ? Carbon::createFromFormat('Y-m-d', $data->end_date) : null;
        $data->start_date = $startDate ? $startDate->format('d-m-Y') : null;
        $data->end_date = $endDate ? $endDate->format('d-m-Y') : null;

        return [
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\Maintenance $maintenance */
            $maintenance = Maintenance::findOrFail($id);
            $maintenance->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.maintenance_delete_success')
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
