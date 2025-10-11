<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class UpdatePaymentSettingsRequest extends CustomFailedValidation
{
    private const OPTIONAL_STRING = 'sometimes|string';

    public function rules(): array
    {
        return [
            'group_id'      => 'required|integer',
            'paypal_key'    => self::OPTIONAL_STRING,
            'paypal_secret' => self::OPTIONAL_STRING,
            'stripe_key'    => self::OPTIONAL_STRING,
            'stripe_secret' => self::OPTIONAL_STRING,
            // Add other payment settings fields as needed
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
