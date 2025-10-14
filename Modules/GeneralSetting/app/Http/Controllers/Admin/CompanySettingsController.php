<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\CompanySettingRequest;
use Modules\GeneralSetting\Http\Requests\ListCompanyRequest;
use Modules\GeneralSetting\Http\Requests\SettingListRequest;

class CompanySettingsController extends GeneralSettingBaseController
{
    public function company(): View
    {
        return view('generalsetting::company.index');
    }

    public function store(CompanySettingRequest $request): JsonResponse
    {
        try {
            $this->repository->storeCompanySettings($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.company_setting_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function listCompany(ListCompanyRequest $request): JsonResponse
    {
        try {
            $data = $this->repository->getCompanySettings($request->group_id);

            if (!$data) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data'   => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function list(SettingListRequest $request): JsonResponse
    {
        try {
            $settings = $this->repository->getSettingsByGroup($request->validated()['group_id']);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.setting_retrive_success'),
                'data'    => $settings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
