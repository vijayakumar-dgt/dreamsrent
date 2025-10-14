<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiConfigurationController extends GeneralSettingBaseController
{
    public function aiConfiguration(): View
    {
        return view('generalsetting::website_settings.ai_configuration');
    }

    public function updateAiConfiguration(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id'          => ['required', 'integer'],
            'ai_api_key'        => ['required', 'string', 'min:10'],
            'ai_global_status'  => ['nullable', 'boolean'],
            'ai_admin_status'   => ['nullable', 'boolean'],
            'ai_user_status'    => ['nullable', 'boolean'],
        ]);

        $settings = [
            'group_id'         => (int) $validated['group_id'],
            'ai_api_key'       => $validated['ai_api_key'],
            'ai_global_status' => isset($validated['ai_global_status']) ? (int) $validated['ai_global_status'] : 0,
            'ai_admin_status'  => isset($validated['ai_admin_status']) ? (int) $validated['ai_admin_status'] : 0,
            'ai_user_status'   => isset($validated['ai_user_status']) ? (int) $validated['ai_user_status'] : 0,
        ];

        try {
            $this->repository->saveNotificationSettings($settings);
            $this->repository->updateEnvVariables(['OPENAI_API_KEY' => $validated['ai_api_key']]);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.ai_configuration_update_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
