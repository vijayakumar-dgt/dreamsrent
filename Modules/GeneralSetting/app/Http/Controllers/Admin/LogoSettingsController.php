<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\StoreLogoSettingsRequest;

class LogoSettingsController extends GeneralSettingBaseController
{
    public function logo(): View
    {
        return $this->logoSettings();
    }

    public function logoSettings(): View
    {
        return view('generalsetting::website_settings.logo-setting');
    }

    public function storeLogoSettings(StoreLogoSettingsRequest $request): JsonResponse
    {
        try {
            $files = $request->only(['logo_image', 'favicon_image', 'small_image', 'dark_logo']);
            $paths = $this->settingsManager->storeLogoSettings($files);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.logo_update_success'),
                'data'    => $paths,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.logo_setting_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
