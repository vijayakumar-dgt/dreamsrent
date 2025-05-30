<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Checklist;
use Modules\CarInfo\Models\Inspection;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Repositories\Contracts\InspectionRepositoryInterface;

class InspectionRepository implements InspectionRepositoryInterface
{
    public function index(): array
    {
        $users = User::select('users.id', 'users.name', 'user_details.first_name', 'user_details.last_name')
            ->join('user_details', 'user_details.user_id', '=', 'users.id')
            ->where('users.user_type', 2)
            ->where('users.status', 1)
            ->orderBy('user_details.first_name', 'asc')->get()->map(function ($user) {
                $user->name = $user->first_name ? ucwords($user->first_name . ' ' . $user->last_name) : '';
                return $user;
            });
        $checklists = Checklist::where('status', true)->orderBy('name', 'asc')->get();
        $data = [
            'users' => $users,
            'checklists' => $checklists
        ];

        return $data;
    }

    public function store(Request $request): array
    {
        $successMessage = empty($request->id) ? __('admin.rentals.inspection_create_success') : __('admin.rentals.inspection_update_success');
        $errorMessage = empty($request->id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            if ($request->has('id') && $request->id != null) {
                /** @var \Modules\CarInfo\Models\Inspection */
                $inspection = Inspection::find($request->id);
                if ($inspection == null) {
                    return [
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Inspection not found.',
                    ];
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
            return [
                'status' => 'success',
                'code'   => 200,
                'data' => $inspection,
                'message' => $successMessage
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'code'   => 422,
                'message' => $errorMessage
            ];
        }
    }

    public function getAll(Request $request): array
    {
        try {
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
                    $inspection->inspector->name = $inspection->inspector->name;
                    if ($inspection->inspector->userDetails) {
                        $inspection->inspector->name = $inspection->inspector->userDetails->first_name
                            ? ucwords($inspection->inspector->userDetails->first_name . ' ' . $inspection->inspector->userDetails->last_name)
                            : $inspection->inspector->name;
                        $inspection->inspector->profile_image = uploadedAsset($inspection->inspector->userDetails->profile_image ?? null, 'profile');
                    }
                }
                if ($inspection->car) {
                    $inspection->car->vehicle_image = uploadedAsset($inspection->car->vehicle_image ?? null, 'default');
                }
                unset($inspection->inspector->userDetails);
                return $inspection;
            });

            return [
                'status' => 'success',
                'code' => 200,
                'data' => $inspections
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getById(int $id): array
    {
        $inspection = Inspection::with('car', 'inspector')->find($id);

        if (!$inspection) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        return [
            'status' => 'success',
            'code'   => 200,
            'data' => $inspection
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\Inspection $inspection */
            $inspection = Inspection::findOrFail($id);
            $inspection->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.inspection_delete_success')
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
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getVehicles(Request $request): array
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

        return [
            'status' => 'success',
            'code' => 200,
            'data' => $vehicles
        ];
    }

    public function checkVehicleInspection(Request $request): array
    {
        $inspection_date = Carbon::parse($request->inspection_date)->format('Y-m-d');
        $exists = Inspection::where('vehicle_info_id', $request->vehicle_info_id)
            ->where('inspection_date', $inspection_date)
            ->when($request->inspection_id, function ($query) use ($request) {
                $query->where('id', '!=', $request->inspection_id);
            })
            ->exists();

        return ['exists' => $exists];
    }

}