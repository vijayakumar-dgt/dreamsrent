<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrefixSettingsController extends GeneralSettingBaseController
{
    public function prefixes(): View
    {
        return view('generalsetting::website_settings.prefixes');
    }

    public function updatePrefixes(Request $request): JsonResponse
    {
        try {
            $this->repository->updatePrefixes($request->all(), $request->group_id);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.prefix_settings_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
