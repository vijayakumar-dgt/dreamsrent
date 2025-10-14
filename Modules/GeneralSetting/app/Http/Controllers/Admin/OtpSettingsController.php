<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\StoreOtpSettingsRequest;

class OtpSettingsController extends GeneralSettingBaseController
{
    public function otpSettings(): View
    {
        return view('generalsetting::website_settings.otp-setting');
    }

    public function storeOtpSettings(StoreOtpSettingsRequest $request): JsonResponse
    {
        try {
            $this->repository->storeOtpSettings($request->validated());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.otp_success'),
                'data'    => [],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }
}
