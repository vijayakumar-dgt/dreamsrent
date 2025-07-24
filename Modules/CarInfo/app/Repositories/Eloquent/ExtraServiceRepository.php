<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Services\ImageResizer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\ExtraService;
use Modules\CarInfo\Repositories\Contracts\ExtraServiceRepositoryInterface;

class ExtraServiceRepository implements ExtraServiceRepositoryInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function store(Request $request): array
    {
        $id = $request->id ?? '';
        $successMessage = empty($id) ? __('admin.rentals.extra_service_create_success') : __('admin.rentals.extra_service_update_success');
        $errorMessage = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            if (!$request->filled('id')) {
                /** @var \Modules\CarInfo\Models\ExtraService */
                $extraService = new ExtraService();
            } else {
                /** @var \Modules\CarInfo\Models\ExtraService */
                $extraService = ExtraService::find($id);
                if ($extraService == null) {
                    return [
                        'status'  => 'error',
                        'code'    => 422,
                        'message' => __('admin.common.default_update_error')
                    ];
                }
                $extraService->status = $request->status == 'on' ? 1 : 0;
            }
            $extraService->name = $request->name;
            $folderName = 'vehicles/extra-service';
            /** @var string $oldIcon */
            $oldIcon = $extraService->icon ?? '';

            /** @var string $oldImage */
            $oldImage = $extraService->image ?? '';

            //check if the file is valid
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                if ($icon && $icon->isValid()) {
                    $extraService->icon = $this->imageResizer->uploadFile($icon, $folderName, $oldIcon);
                }
            }
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image && $image->isValid()) {
                    $extraService->image = $this->imageResizer->uploadFile($image, $folderName, $oldImage);
                }
            }
            $extraService->description = $request->description;
            $extraService->save();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMessage
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMessage
            ];
        }
    }

    public function getAll(Request $request): array
    {
        try {
            $authId = current_user();
            $language_id = $authId->language_id ?? null;
            $extraServices = ExtraService::query()->where("language_id", $language_id);
            if ($request->has('keyword') && $request->keyword != "") {
                $extraServices->where('name', 'like', '%' . $request->keyword . '%');
            }
            if ($request->has('status') && $request->status != "") {
                $extraServices->where('status', $request->status);
            }
            $extraServices = $extraServices->orderBy('name', 'asc')->get();
            //replace image path
            $extraServices->map(function ($extraService) {
                $iconPath = is_string($extraService->icon) ? $extraService->icon : '';
                $extraService->icon = uploadedAsset($iconPath ?? '');

                $imagePath = is_string($extraService->image) ? $extraService->image : '';
                $extraService->image = uploadedAsset($imagePath ?? '');
            });

            return [
                'status' => 'success',
                'code'   => 200,
                'data'   => $extraServices
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getById(int $id): array
    {
        $data = ExtraService::where("id", $id)->first();

        if (!$data) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        if ($data) {
            $iconPath = is_string($data->icon) ? $data->icon : '';
            $data->icon = uploadedAsset($iconPath ?? '');
            $imagePath = is_string($data->image) ? $data->image : '';
            $data->image = uploadedAsset($imagePath ?? '');
        }

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\ExtraService $extraService */
            $extraService = ExtraService::findOrFail($id);
            $extraService->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.extra_service_delete_success')
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

    public function getVehicleExtraServices(Request $request): array
    {
        try {
            $vehicleIds = $request->vehicle_ids;

            $extraServices = ExtraService::select(
                'extra_services.id',
                'extra_services.name',
                'extra_services.description',
                'vehicle_extra_services.value as extra_service_type',
                'vehicle_extra_services.price'
            )
                ->Join('vehicle_extra_services', 'extra_services.id', '=', 'vehicle_extra_services.extra_service_id')
                ->where('extra_services.status', 1)
                ->whereIn('vehicle_id', $vehicleIds)
                ->get()->map(function ($extraService) {
                    $extraService->price = $extraService->price ?? 0;
                    return $extraService;
                });

            return [
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $extraServices,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }
}
