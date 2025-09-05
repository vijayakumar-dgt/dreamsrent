<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class StoreNotificationSettingsRequest extends CustomFailedValidation
{
    /**
     * Common validation rule for required boolean fields.
     */
    private const REQUIRED_BOOLEAN = 'required|boolean';

    public function authorize(): bool
    {
        return true; // Add your auth logic if needed
    }

    public function rules(): array
    {
        return [
            'group_id'                => 'required|integer',
            'notificationPreference'  => 'required',
            'desktopNotifications'    => self::REQUIRED_BOOLEAN,
            'bookingUpdates'          => self::REQUIRED_BOOLEAN,
            'paymentNotifications'    => self::REQUIRED_BOOLEAN,
            'vehicleManagement'       => self::REQUIRED_BOOLEAN,
            'unreadBadge'             => self::REQUIRED_BOOLEAN,
            'userTenantNotifications' => self::REQUIRED_BOOLEAN,
            'discountOffers'          => self::REQUIRED_BOOLEAN,
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
