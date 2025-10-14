<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Repositories\Contracts\VehicleManagementRepositoryInterface;
use Modules\CarInfo\Repositories\Support\VehicleFormDataService;
use Modules\CarInfo\Repositories\Support\VehicleInfoWriter;
use Modules\CarInfo\Repositories\Support\VehicleMediaService;

class VehicleInfoManagementRepository implements VehicleManagementRepositoryInterface
{
    public function __construct(
        private readonly VehicleFormDataService $formDataService,
        private readonly VehicleInfoWriter $vehicleInfoWriter,
        private readonly VehicleMediaService $vehicleMediaService
    ) {
    }

    public function createVehicle(): array
    {
        return $this->formDataService->getCreateData();
    }

    public function editVehicle(string $slug, Request $request): array
    {
        return $this->formDataService->getEditData($slug, $request);
    }

    public function delete(int|array $id): array
    {
        try {
            if (is_array($id) && !empty($id)) {
                VehicleInfo::whereIn('id', $id)->delete();
            } else {
                $vehicle = VehicleInfo::findOrFail($id);
                $vehicle->delete();
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.vehicle_delete_success'),
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }

    public function setPopular(Request $request)
    {
        try {
            $vehicle = VehicleInfo::find($request->id);

            if (!$vehicle) {
                return [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->popular = $request->popular ? 1 : 0;
            $vehicle->save();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.popular_status_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ];
        }
    }

    public function setRecommended(Request $request)
    {
        try {
            $vehicle = VehicleInfo::find($request->id);

            if (!$vehicle) {
                return [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->recommended = $request->recommended ? 1 : 0;
            $vehicle->save();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.recommended_status_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ];
        }
    }

    public function setStatus(Request $request)
    {
        try {
            $vehicle = VehicleInfo::find($request->vehicle_id);

            if (!$vehicle) {
                return [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ];
            }

            $vehicle->status = $request->status ? 1 : 0;
            $vehicle->save();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.common.default_status_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_status_error'),
            ];
        }
    }

    public function deleteVehiclePolicy(Request $request): array
    {
        return $this->vehicleMediaService->deleteVehiclePolicy($request);
    }

    public function deleteVehicleImage(Request $request): array
    {
        return $this->vehicleMediaService->deleteVehicleImage($request);
    }

    public function createVehicleInfo(Request $request)
    {
        try {
            $this->vehicleInfoWriter->create($request);

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.rentals.vehicle_create_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_create_error'),
            ];
        }
    }

    public function updateVehicleInfo(Request $request): array
    {
        try {
            $this->vehicleInfoWriter->update($request);

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.rentals.vehicle_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ];
        }
    }
}
