<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Services\ImageResizer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Repositories\Contracts\VehicleTypeRepositoryInterface;

class VehicleTypeRepository implements VehicleTypeRepositoryInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function store(Request $request): array
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = currentUser();
        if (!$authUser) {
            return [
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ];
        }

        $language_id = $authUser->language_id;
        $id = $request->id ?? null;
        $folderPath = 'vehicles/types';

        $successMessage = empty($id)
            ? __('admin.rentals.vehicle_type_added')
            : __('admin.rentals.vehicle_type_updated');
        $errorMessage = empty($id)
            ? __('admin.common.default_create_error')
            : __('admin.common.default_update_error');

        try {
            $vehicleType = $id ? Cartype::findOrFail($id) : null;

            $category = Category::find($request->vehicle_category_id);

            $data = [
                'name'        => $request->name,
                'category_id' => $request->vehicle_category_id,
                'type'        => $category?->slug,
                'language_id' => $request->language_id ?? ($vehicleType?->language_id ?? $language_id),
                'status'      => $id ? ($request->input('status') == 'on' ? 1 : 0) : 1,
            ];

            // Handle image uploads
            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $data['icon'] = $this->imageResizer->uploadFile($file, $folderPath, $vehicleType?->icon);
            }

            // Create or Update
            Cartype::updateOrCreate(['id' => $id], $data);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMessage,
            ];
        } catch (\Throwable $e) {
            $code = $e instanceof ModelNotFoundException ? 404 : 500;
            $message = $e instanceof ModelNotFoundException
                ? __('admin.common.not_found')
                : $errorMessage;

            return [
                'status'  => 'error',
                'code'    => $code,
                'message' => $message,
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getAll(Request $request): array
    {
        try {
            $pageLength = $request->length;
            $offset = $request->start;
            /** @var \App\Models\User|null $authUser */
            $authUser = currentUser();
            if (!$authUser) {
                return [
                    'status'  => 'error',
                    'code'    => 401,
                    'message' => 'Unauthorized: User not authenticated.'
                ];
            }
            $language_id = $authUser->language_id;
            $cartypes = Cartype::query()->where("language_id", $language_id);

            if ($request->has('search') && $request->search != null) {
                $cartypes->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->has('status') && $request->status != "") {
                $cartypes->where('status', $request->status);
            }

            $filteredRecords = $cartypes->count();
            $totalRecords = Cartype::where("language_id", $language_id)->count();

            $cartypes = $cartypes->orderBy('name', 'asc')
                ->skip($offset)
                ->take($pageLength)
                ->get();

            $cartypes = $cartypes->map(function ($cartype) {
                return [
                    'id'     => $cartype->id,
                    'name'   => $cartype->name,
                    'icon'   => uploadedAsset($cartype->icon ?? '', 'default'),
                    'status' => $cartype->status,
                ];
            });

            return [
                'draw'            => $request->draw,
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $cartypes,
                'code'            => 200
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
        $data = Cartype::where("id", $id)->first();

        if (!$data) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        if ($data) {
            $data->icon = uploadedAsset($data->icon);
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
            /** @var \Modules\CarInfo\Models\Cartype $type */
            $type = Cartype::findOrFail($id);
            $type->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.vehicle_type_deleted')
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.rentals.vehicle_type_not_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getVehicleTypes(Request $request): array
    {
        try {
            $orderBy = $request->order_by ?? 'asc';
            $search = $request->search ?? null;

            $carTypes = Cartype::when($search, function ($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            })
                ->orderBy('id', $orderBy)
                ->where('status', 1)
                ->get();

            return [
                'status' => 'success',
                'code'   => 200,
                'data'   => $carTypes
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }
}
