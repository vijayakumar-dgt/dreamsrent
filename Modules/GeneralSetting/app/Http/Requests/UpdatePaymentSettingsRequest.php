<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentSettingsRequest extends FormRequest
{
   public function rules(): array
    {
        return [
            'group_id' => 'required|integer',
            'paypal_key' => 'sometimes|string',
            'paypal_secret' => 'sometimes|string',
            'stripe_key' => 'sometimes|string',
            'stripe_secret' => 'sometimes|string',
            // Add other payment settings fields as needed
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
