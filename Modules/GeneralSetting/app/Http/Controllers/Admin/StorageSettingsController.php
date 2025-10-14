<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Exceptions\AwsSettingsUpdateException;
use Modules\GeneralSetting\Exceptions\StorageStatusUpdateException;
use Modules\GeneralSetting\Http\Requests\StorageStatusUpdateRequest;
use Modules\GeneralSetting\Http\Requests\StoreAwsSettingsRequest;

class StorageSettingsController extends GeneralSettingBaseController
{
    public function storage(): View
    {
        return view('generalsetting::other_settings.storage-setting');
    }

    public function storageStatusUpdate(StorageStatusUpdateRequest $request): JsonResponse
    {
        try {
            $success = $this->repository->updateStorageStatus(
                $request->storage_type,
                (bool) $request->status
            );

            if (!$success) {
                throw new StorageStatusUpdateException(__('admin.general_settings.update_failed'));
            }

            $action = $request->status == 1 ? 'activated' : 'blocked';
            $message = ucfirst(str_replace('_', ' ', $request->storage_type)) . " {$action}";

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (StorageStatusUpdateException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }

    public function storeAwsSettings(StoreAwsSettingsRequest $request): JsonResponse
    {
        try {
            $settings = [
                'aws_access_key'  => $request->aws_access_key,
                'aws_secret_key'  => $request->aws_secret_key,
                'aws_region'      => $request->aws_region,
                'aws_bucket_name' => $request->aws_bucket_name,
                'aws_base_url'    => $request->aws_base_url,
            ];

            $success = $this->repository->updateAwsSettings($settings);

            if (!$success) {
                throw new AwsSettingsUpdateException(__('admin.general_settings.update_failed'));
            }

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.aws_success'),
                'data'    => [],
            ]);
        } catch (AwsSettingsUpdateException $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }
}
