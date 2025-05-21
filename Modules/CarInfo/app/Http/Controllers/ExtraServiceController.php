<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CarInfo\Models\ExtraService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ExtraServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('carinfo::extra_services.index');
    }


    /**
     * Save or update an extra service.
     *
     * Validates the request data and either creates a new extra service or updates an existing one
     * based on the presence of an 'id' in the request. Handles file uploads for 'icon' and 'image'
     * fields, ensuring they are of valid mime types and size.
     *
     * @param \Illuminate\Http\Request $request The request object containing input data.
     * @return \Illuminate\Http\JsonResponse JSON response with a success or error message.
     */

    public function storeExtraService(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('extra_services')->ignore($request->id)->whereNull('deleted_at'),
                'max:30',
                'min:3',
                'not_regex:/<\/?script\b[^>]*>/i',
            ],
            'icon' => [
                'mimes:jpeg,jpg,png,svg',
                'max:2048'
            ],
            'image' => [
                'mimes:jpeg,jpg,png,svg',
                'max:2048'
            ],
            'description' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i',
            ],
        ], [
            'name.required' => __('admin.rentals.name_required'),
            'name.unique' => __('admin.manage.name_unique'),
            'name.max' => __('admin.rentals.name_maxlength'),
            'name.min' => __('admin.rentals.name_minlength'),
            'icon.mimes' => __('admin.rentals.icon_extension'),
            'icon.max' => __('admin.rentals.image_size', ['size' => 2]),
            'image.mimes' => __('admin.rentals.extra_service_image_format'),
            'image.max' => __('admin.common.image_size', ['size' => 2]),
            'description.required' => __('admin.rentals.description_required'),
            'name.not_regex' => __('admin.common.script_tag_not_allowed'),
            'description.not_regex' => __('admin.common.script_tag_not_allowed'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }
        $successMessage = empty($request->id) ? __('admin.rentals.extra_service_create_success') : __('admin.rentals.extra_service_update_success');
        $errorMessage = empty($request->id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');
        try {
            if (!$request->filled('id')) {
                /** @var \Modules\CarInfo\Models\ExtraService */
                $extraService = new ExtraService();
            } else {
                /** @var \Modules\CarInfo\Models\ExtraService */
                $extraService = ExtraService::find($request->id);
                if ($extraService == null) {
                    return response()->json([
                        'status' => 'error',
                        'code'   => 422,
                        'message' => __('admin.common.default_update_error')
                    ], 422);
                }
                $extraService->status = $request->status == 'on' ? 1 : 0;
            }
                $extraService->name = $request->name;
                $folderName = 'extra_services';
                /** @var string $oldIcon */
                $oldIcon = str_replace($folderName . '/', '', $extraService->icon ?? '');

                /** @var string $oldImage */
                $oldImage = str_replace($folderName . '/', '', $extraService->image ?? '');

            //check if the file is valid
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                if ($icon && $icon->isValid()) {
                    $extraService->icon = uploadFile($icon, $folderName, $oldIcon);
                }
            }
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image && $image->isValid()) {
                    $extraService->image = uploadFile($image, $folderName, $oldImage);
                }
            }
                $extraService->description = $request->description;
                $extraService->save();
                return response()->json([
                    'status' => 'success',
                    'code'   => 200,
                    'message' => $successMessage
                ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Retrieve a list of extra services ordered by ID in descending order.
     * The function replaces the image paths with their respective URLs if the files exist.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the status, code, and list of extra services.
     */


    public function getExtraServices(Request $request): JsonResponse
    {
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
            $extraService->icon = ($iconPath !== '' && file_exists(public_path('storage/' . $iconPath)))
                ? uploadedAsset($iconPath)
                : uploadedAsset('default.png');

             $imagePath = is_string($extraService->image) ? $extraService->image : '';
                $extraService->image = ($imagePath !== '' && file_exists(public_path('storage/' . $imagePath)))
                ? uploadedAsset($imagePath)
                : uploadedAsset('default.png');
        });


        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $extraServices
        ]);
    }

    /**
     * Retrieve a specific extra service by its ID.
     * The function replaces the icon and image paths with their respective URLs if the files exist.
     *
     * @param int $id The ID of the extra service to retrieve.
     * @return \Illuminate\Http\JsonResponse JSON response containing the status, code, and extra service data.
     */

    public function getExtraService($id): JsonResponse
    {
        $extraService = ExtraService::find($id);
        if ($extraService) {
            $iconPath = is_string($extraService->icon) ? $extraService->icon : '';
            $extraService->icon = $iconPath !== "" && file_exists(public_path('storage/' . $iconPath))
                ? uploadedAsset($iconPath)
                : uploadedAsset('default.png');
            $imagePath = is_string($extraService->image) ? $extraService->image : '';
            $extraService->image = $imagePath !== "" && file_exists(public_path('storage/' . $imagePath))
                ? uploadedAsset($imagePath)
                : uploadedAsset('default.png');

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data' => $extraService
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => __('admin.common.default_retrieve_error')
            ], 422);
        }
    }

    /**
     * Delete an extra service.
     *
     * @param \Illuminate\Http\Request $request Contains the ID of the extra service to delete.
     * @return \Illuminate\Http\JsonResponse JSON response containing the status, code, and message.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the extra service is not found.
     * @throws \Throwable If any other error occurs.
     */
    public function deleteExtraService(Request $request): JsonResponse
    {
        try {
            $extraService = ExtraService::where('id', $request->delete_id)->firstOrFail();
            $extraService->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.extra_service_delete_success')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => __('admin.common.default_delete_error')
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => __('admin.common.default_delete_error')
            ], 422);
        }
    }

    public function getVehicleExtraServices(Request $request): JsonResponse
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

            return response()->json([
                'code'   => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $extraServices,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
