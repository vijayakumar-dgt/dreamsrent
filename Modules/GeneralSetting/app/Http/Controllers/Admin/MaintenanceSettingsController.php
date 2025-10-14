<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\StoreMaintenanceSettingsRequest;

class MaintenanceSettingsController extends GeneralSettingBaseController
{
    public function maintenance(): View
    {
        return view('generalsetting::maintenance.index');
    }

    public function storeMaintenanceSettings(StoreMaintenanceSettingsRequest $request): JsonResponse
    {
        try {
            $this->repository->storeMaintenanceSettings($request->all());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.maintanance_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.maintanance_update_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
