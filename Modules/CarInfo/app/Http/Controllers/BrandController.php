<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\CarInfo\Models\Brand;
use Illuminate\Http\UploadedFile;

class BrandController extends Controller
{
    public function index(): View
    {
        return view('carinfo::brand.index');
    }

    public function store(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }
        $language_id = $authUser->language_id;

        $id = $request->id ?? '';

        $data = [
            'brand_name' => $request->brand_name,
            'total_cars' => $request->total_cars,
        ];

        $validator = Validator::make($request->all(), [
            'brand_name' => [
                'required',
                'max:30',
                'min:3',
                Rule::unique('brands')->ignore($id)->whereNull('deleted_at'),
                'not_regex:/<\/?script\b[^>]*>/i'
            ],
            'brand_image' => 'mimes:jpeg,jpg,png,svg|max:2048',
            'brand_icon' => 'mimes:jpeg,jpg,png,svg|max:2048',
            'total_cars' => [
                'max:255',
            ]
        ], [
            'brand_name.required' => __('admin.rentals.brand_name_required'),
            'brand_name.max' => __('admin.rentals.brand_name_maxlength'),
            'brand_name.min' => __('admin.rentals.brand_name_minlength'),
            'brand_name.unique' => __('admin.rentals.brand_name_unique'),
            'brand_name.not_regex' => __('admin.common.script_tag_not_allowed'),
            'total_cars.required' => __('admin.rentals.total_vehicles_required'),
            'brand_image.mimes' => __('admin.rentals.brand_image_format'),
            'brand_image.max' => __('admin.rentals.brand_image_size', ['size' => 2]),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.rentals.brand_create_success') : __('admin.rentals.brand_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            if (empty($id)) {
                // CREATE
                $data['language_id'] = $language_id;

                if ($request->hasFile('brand_image')) {
                    $file = $request->file('brand_image');
                    if ($file instanceof UploadedFile) {
                        $data['brand_image'] = uploadFile($file, 'vehicles/brands');
                    }
                }

                if ($request->hasFile('brand_icon')) {
                    $file = $request->file('brand_icon');
                    if ($file instanceof UploadedFile) {
                        $data['brand_icon'] = uploadFile($file, 'vehicles/brands');
                    }
                }

                Brand::create($data);
            } else {
                // UPDATE
                $brand = Brand::where("id", $id)->first();

                if (!$brand) {
                    return response()->json([
                        'status' => 'error',
                        'code' => 404,
                        'message' => __('admin.common.not_found')
                    ], 404);
                }

                $oldImage = $brand->brand_image;
                $oldIcon = $brand->brand_icon;

                if ($request->hasFile('brand_image')) {
                    $file = $request->file('brand_image');
                    if ($file instanceof UploadedFile) {
                        $data['brand_image'] = uploadFile($file, 'vehicles/brands', $oldImage);
                    }
                }

                if ($request->hasFile('brand_icon')) {
                    $file = $request->file('brand_icon');
                    if ($file instanceof UploadedFile) {
                        $data['brand_icon'] = uploadFile($file, 'vehicles/brands', $oldIcon);
                    }
                }

                $data['status'] = $request->status ?? 1;
                $data['language_id'] = $request->language_id ?? $brand->language_id;

                $brand->update($data);
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
            /** @var \App\Models\User|null $authUser */
            $authUser = current_user();
            if (!$authUser) {
                return response()->json([
                    'status' => 'error',
                    'code'   => 401,
                    'message' => 'Unauthorized: User not authenticated.'
                ], 401);
            }
            $language_id = $authUser->language_id;
            $query = Brand::query()->where("language_id", $language_id);

            // Search
            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('brand_name', 'like', "%{$search}%")
                        ->orWhere('total_cars', 'like', "%{$search}%");
                });
            }

            // Status Filter
            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('brands.status', $status);
            }

            // Ordering
            $columnIndex = $request->order[0]['column'] ?? 1;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'brand_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            // Validate column names to avoid SQL injection
            if (in_array($columnName, ['brand_name', 'total_cars', 'status'])) {
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
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data' => $data,
            ], 200);
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
        $data = Brand::where("id", $id)->first();
        if ($data) {
            $data->brand_image = uploadedAsset($data->brand_image);
            $data->brand_icon = uploadedAsset($data->brand_icon);
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
            Brand::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.brand_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function getBrands(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'desc';
        $search = $request->search ?? null;

        try {
            $data = Brand::when($search, function ($query) use ($search) {
                    return $query->where('brand_name', 'LIKE', "%{$search}%");
            })
                ->orderBy('id', $orderBy)
                ->where('status', 1)
                ->get(['id', 'brand_name'])
                ->map(function ($brand) {
                    $imagePath = public_path("storage/brands/{$brand->brand_image}");
                    $brand->brand_image = file_exists($imagePath) ? url("storage/brands/{$brand->brand_image}") : null;
                    return $brand;
                });

            return response()->json([
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
