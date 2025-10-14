<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\UpdateThemeSettingsRequest;

class ThemeSettingsController extends GeneralSettingBaseController
{
    public function themeSettings(): View
    {
        return view('generalsetting::website_settings.theme_settings');
    }

    public function updateThemeSettings(UpdateThemeSettingsRequest $request): JsonResponse
    {
        try {
            $this->repository->updateThemeSettings($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.theme_update_success'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ], 500);
        }
    }
}
