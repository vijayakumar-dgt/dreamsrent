<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add your auth logic if needed
    }

    public function rules(): array
    {
        return [
            'group_id'                   => 'required|integer',
            'notificationPreference'     => 'required',
            'desktopNotifications'       => 'required|boolean',
            'bookingUpdates'             => 'required|boolean',
            'paymentNotifications'       => 'required|boolean',
            'vehicleManagement'          => 'required|boolean',
            'unreadBadge'                => 'required|boolean',
            'userTenantNotifications'    => 'required|boolean',
            'discountOffers'             => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.required' => __('admin.general_settings.validation_error'),
            // Add additional custom messages if needed
        ];
    }
}
