<?php

namespace Modules\CarInfo\Repositories;

use App\Services\ImageResizer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Modules\CarInfo\Models\Brand;

class BrandRepository
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function store(Request $request): array
    {
        $authUser = current_user();
        $language_id = $authUser->language_id;
        $id = $request->id ?? null;

        $folderPath = 'vehicles/brands';

        try {
            // Get existing brand (if any)
            $brand = $id ? Brand::find($id) : null;

            if ($id && !$brand) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => __('admin.common.not_found')
                ];
            }

            $data = [
                'brand_name'   => $request->brand_name,
                'language_id'  => $request->language_id ?? ($brand->language_id ?? $language_id),
                'status'       => $request->status ?? ($brand->status ?? 1),
            ];

            // Handle image uploads
            if ($request->hasFile('brand_image')) {
                $file = $request->file('brand_image');
                $data['brand_image'] = $this->imageResizer->uploadFile($file, $folderPath, $brand->brand_image ?? null);
            }

            if ($request->hasFile('brand_icon')) {
                $file = $request->file('brand_icon');
                $data['brand_icon'] = $this->imageResizer->uploadFile($file, $folderPath, $brand->brand_icon ?? null);
            }

            // Create or Update
            Brand::updateOrCreate(['id' => $id], $data);

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => empty($id) 
                    ? __('admin.rentals.brand_create_success') 
                    : __('admin.rentals.brand_update_success')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => empty($id) 
                    ? __('admin.common.default_create_error') 
                    : __('admin.common.default_update_error'),
                'error' => $e->getMessage()
            ];
        }
    }


}