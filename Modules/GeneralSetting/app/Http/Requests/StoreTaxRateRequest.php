<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaxRateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tax_name' => [
                'required',
                'max:30',
                'min:3',
                Rule::unique('tax_rates')->ignore($this->id)->whereNull('deleted_at')
            ],
            'tax_rate' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'tax_name.required' => __('admin.general_settings.tax_name_required'),
            'tax_name.min' => __('admin.general_settings.tax_name_minlength'),
            'tax_name.max' => __('admin.general_settings.tax_name_maxlength'),
            'tax_name.unique' => __('admin.general_settings.tax_name_unique'),
            'tax_rate.required' => __('admin.general_settings.tax_rate_required'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
