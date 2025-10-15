<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Exceptions\PaymentSettingsUpdateException;
use Modules\GeneralSetting\Exceptions\PaymentStatusUpdateException;
use Modules\GeneralSetting\Http\Requests\UpdatePaymentSettingsRequest;
use Modules\GeneralSetting\Http\Requests\UpdatePaymentStatusRequest;

class PaymentSettingsController extends GeneralSettingBaseController
{
    public function paymentIndex(): View
    {
        return view('generalsetting::payment.index');
    }

    public function updatepaymentSettings(UpdatePaymentSettingsRequest $request): JsonResponse
    {
        try {
            $success = $this->paymentSettings->updatePaymentSettings($request->all());

            if (!$success) {
                throw new PaymentSettingsUpdateException(__('admin.general_settings.update_failed'));
            }

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.payment_updated_successfull'),
            ]);
        } catch (PaymentSettingsUpdateException $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.global_settings_error') . $th->getMessage(),
            ], 500);
        }
    }

    public function updatepaymentStatus(UpdatePaymentStatusRequest $request): JsonResponse
    {
        try {
            $success = $this->paymentSettings->updatePaymentStatus($request->all());

            if (!$success) {
                throw new PaymentStatusUpdateException(__('admin.general_settings.update_failed'));
            }

            return response()->json([
                'success' => true,
                'message' => __('admin.general_settings.payment_updated_successfull'),
            ]);
        } catch (PaymentStatusUpdateException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.global_settings_error') . $th->getMessage(),
            ], 500);
        }
    }

    public function paymentList(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'desc';
        $groupId = 13;

        try {
            $data = $this->settingsRetriever->getPaymentSettings($groupId, $orderBy);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.general_settings_success'),
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.global_settings_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
