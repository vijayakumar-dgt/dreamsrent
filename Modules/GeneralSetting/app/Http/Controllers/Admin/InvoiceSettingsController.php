<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\StoreInvoiceSettingsRequest;

class InvoiceSettingsController extends GeneralSettingBaseController
{
    public function invoiceSettings(): View
    {
        return view('generalsetting::app_settings.invoice-setting');
    }

    public function storeInvoiceSettings(StoreInvoiceSettingsRequest $request): JsonResponse
    {
        try {
            $this->repository->saveInvoiceSettings($request->validated());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.invoice_setting_success'),
                'data'    => [],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.invoice_setting_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
