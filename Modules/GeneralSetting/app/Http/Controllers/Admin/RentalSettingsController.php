<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\StoreRentalSettingsRequest;

class RentalSettingsController extends GeneralSettingBaseController
{
    public function rentalSettings(): View
    {
        return view('generalsetting::rental_settings.rental-settings');
    }

    public function storeRentalSettings(StoreRentalSettingsRequest $request): JsonResponse
    {
        try {
            $this->repository->saveRentalSettings($request->validated());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.rental_saved_successfully'),
                'data'    => [],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
