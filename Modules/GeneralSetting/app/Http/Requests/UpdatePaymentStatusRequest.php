<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'key' => 'required|string',
            'value' => 'required|in:0,1',
            'group_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'value.in' => __('admin.general_settings.status_must_be_0_or_1'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
