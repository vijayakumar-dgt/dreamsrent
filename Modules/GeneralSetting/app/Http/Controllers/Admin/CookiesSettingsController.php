<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\CookiesSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreCookiesSettingsRequest;
use Modules\GeneralSetting\Models\Language;

class CookiesSettingsController extends GeneralSettingBaseController
{
    public function gdprCookies(): View
    {
        $languages = Language::with('transLang')->get();

        return view('generalsetting::system_settings.gdpr-cookies', compact('languages'));
    }

    public function storeCookiesSettings(StoreCookiesSettingsRequest $request): JsonResponse
    {
        try {
            $this->settingsManager->storeCookiesSettings($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.cookies_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.sretrive_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function cookiesSettingsList(CookiesSettingsRequest $request): JsonResponse
    {
        try {
            $settings = $this->settingsRetriever->getCookiesSettings(
                $request->group_id,
                $request->language_id
            );

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.cookies_retrive_success'),
                'data'    => $settings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
