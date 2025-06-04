<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class StoreTaxGroupRequest extends CustomFailedValidation
{
    public function rules(): array
    {
        return [
            'tax_group_name' => [
                'required',
                'max:30',
                'min:3',
                Rule::unique('tax_groups', 'tax_name')->ignore($this->id)->whereNull('deleted_at')
            ],
            'sub_tax' => 'required|array|min:1',
        ];
    }

    public function messages(): array
    {
        return [
        'tax_group_name.required' => __('admin.general_settings.tax_group_name_required'),
        'tax_group_name.min' => __('admin.general_settings.tax_group_name_minlength'),
        'tax_group_name.max' => __('admin.general_settings.tax_group_name_maxlength'),
        'tax_group_name.unique' => __('admin.general_settings.tax_group_name_unique'),
        'sub_tax.required' => __('admin.general_settings.sub_taxes_required'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
