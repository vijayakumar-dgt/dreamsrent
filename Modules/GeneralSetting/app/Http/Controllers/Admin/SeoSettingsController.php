<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\StoreSeoSetupRequest;

class SeoSettingsController extends GeneralSettingBaseController
{
    public function seosetup(): View
    {
        return view('generalsetting::website_settings.seosetup');
    }

    public function storeSeoSetupSettings(StoreSeoSetupRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $groupId = $request->group_id ?? null;
            $this->settingsManager->storeSeoSettings($data, $groupId);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.seo_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.seo_update_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
