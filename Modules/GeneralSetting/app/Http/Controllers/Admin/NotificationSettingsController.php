<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\StoreNotificationSettingsRequest;

class NotificationSettingsController extends GeneralSettingBaseController
{
    public function notifications(): View
    {
        return view('generalsetting::notifications-setting.index');
    }

    public function storeNotificationSettings(StoreNotificationSettingsRequest $request): JsonResponse
    {
        try {
            $this->repository->saveNotificationSettings($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.notification_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.notification_error_update'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
