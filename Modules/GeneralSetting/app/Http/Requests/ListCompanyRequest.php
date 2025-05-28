<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust if using auth
    }

    public function rules(): array
    {
        return [
            'group_id' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.required' => __('admin.general_settings.validation_error'),
        ];
    }
}
