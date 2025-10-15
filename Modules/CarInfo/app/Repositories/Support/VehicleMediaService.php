<?php

namespace Modules\CarInfo\Repositories\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\CarInfo\Models\VehicleMeta;

class VehicleMediaService extends VehicleRepositoryBase
{
    public function deleteVehicleImage(Request $request): array
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicle_metas,vehicle_id',
            'image_path' => 'required|string',
        ]);

        $response = [
            'code'    => 500,
            'success' => false,
            'message' => __('admin.common.default_delete_error')
        ];

        try {
            $vehicleMeta = VehicleMeta::where('vehicle_id', $request->vehicle_id)
                ->where('key', 'vehicle_image')
                ->first();

            if (!$vehicleMeta) {
                $response = [
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Vehicle images not found.'
                ];
            } else {
                $images = json_decode($vehicleMeta->value, true) ?? [];
                $imageToDelete = parse_url($request->image_path, PHP_URL_PATH);
                $relativePath = is_string($imageToDelete) ? ltrim(str_replace(self::STORAGES, '', $imageToDelete), '/') : null;

                if ($relativePath && ($key = array_search($relativePath, $images)) !== false) {
                    unset($images[$key]);
                    Storage::delete($relativePath);
                    $vehicleMeta->value = json_encode(array_values($images)) ?: '';
                    $vehicleMeta->save();

                    $response = [
                        'code'    => 200,
                        'success' => true,
                        'message' => 'Image deleted successfully.'
                    ];
                } else {
                    $response = [
                        'code'    => 404,
                        'success' => false,
                        'message' => 'Image not found in database.'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Keep default response on exception
        }

        return $response;
    }

    public function deleteVehiclePolicy(Request $request): array
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicle_metas,vehicle_id',
            'file_path'  => 'required|string',
        ]);

        $response = [
            'code'    => 500,
            'success' => false,
            'message' => __('admin.common.default_delete_error')
        ];

        try {
            $filePath = 'vehicles/policy/' . $request->file_path;

            $vehicleMeta = VehicleMeta::where('vehicle_id', $request->vehicle_id)
                ->where('key', 'vehicle_policy')
                ->first();

            if (!$vehicleMeta) {
                return [
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Policy files not found.'
                ];
            }

            $policyFiles = json_decode($vehicleMeta->value, true) ?? [];

            if (($key = array_search($filePath, $policyFiles)) === false) {
                return [
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Policy file not found.'
                ];
            }

            unset($policyFiles[$key]);
            Storage::delete($filePath);
            $vehicleMeta->value = json_encode(array_values($policyFiles)) ?: '';
            $vehicleMeta->save();

            return [
                'code'    => 200,
                'success' => true,
                'message' => 'Policy file deleted successfully.'
            ];
        } catch (\Exception $e) {
            return $response;
        }
    }
}
