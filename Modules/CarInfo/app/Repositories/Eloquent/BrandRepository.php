<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Services\ImageResizer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Repositories\Contracts\BrandRepositoryInterface;

class BrandRepository implements BrandRepositoryInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function store(Request $request): array
    {
        $authUser = currentUser();
        $language_id = $authUser->language_id;
        $id = $request->id ?? null;

        $folderPath = 'vehicles/brands';

        try {
            // Get existing brand (if any)
            $brand = $id ? Brand::find($id) : null;

            if ($id && !$brand) {
                return [
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.not_found')
                ];
            }

            $category = Category::find($request->vehicle_category_id);

            $data = [
                'brand_name'    => $request->brand_name,
                'category_id'   => $request->vehicle_category_id,
                "type"          => $category?->slug ?? null,
                'language_id'   => $request->language_id ?? ($brand->language_id ?? $language_id),
                'status'        => $request->status ?? ($brand->status ?? 1),
            ];

            // Handle image uploads
            foreach (['brand_image', 'brand_icon'] as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $existing = $brand->{$field} ?? null;
                    $data[$field] = $this->imageResizer->uploadFile($file, $folderPath, $existing);
                }
            }

            // Create or Update
            Brand::updateOrCreate(['id' => $id], $data);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => empty($id)
                    ? __('admin.rentals.brand_create_success')
                    : __('admin.rentals.brand_update_success')
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => empty($id)
                    ? __('admin.common.default_create_error')
                    : __('admin.common.default_update_error'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function list(Request $request): array
    {
        try {
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
            $query = Brand::query()->where("language_id", $language_id);

            // Search
            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('brand_name', 'like', "%{$search}%");
                });
            }

            // Status Filter
            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('status', $status);
            }

            // Ordering
            $columnIndex = $request->order[0]['column'] ?? 1;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'brand_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            // Validate column names to avoid SQL injection
            if (in_array($columnName, ['brand_name', 'status'])) {
                $query->orderBy($columnName, $orderDir);
            }

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            // Total Records Count
            $filterTotalRecords = $query->count();
            $totalRecords = Brand::where("language_id", $language_id)->count();

            // Data Fetch
            $data = $query->skip($start)->take($length)->get()->map(function ($brand) {
                $brand->brand_image = uploadedAsset($brand->brand_image);
                $brand->brand_icon = uploadedAsset($brand->brand_icon);
                return $brand;
            });

            // Return Response
            return [
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data'            => $data,
                'code'            => 200,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function edit(int $id): array
    {
        $data = Brand::where("id", $id)->first();

        if (!$data) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        $data->brand_image = uploadedAsset($data->brand_image);
        $data->brand_icon = uploadedAsset($data->brand_icon);

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\Brand $brand */
            $brand = Brand::findOrFail($id);
            $brand->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.brand_delete_success')
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

    public function getBrands(Request $request): array
    {
        $orderBy = $request->order_by ?? 'desc';
        $search = $request->search ?? null;

        try {
            $data = Brand::when($search, function ($query) use ($search) {
                return $query->where('brand_name', 'LIKE', "%{$search}%");
            })
                ->orderBy('id', $orderBy)
                ->where('status', 1)
                ->get(['id', 'brand_name']);

            return [
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $data,
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
