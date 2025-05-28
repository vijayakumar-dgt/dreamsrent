<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceSettingsRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id'                => 'required',
            'maintenance_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'maintenance_description' => 'nullable|string|max:5000',
            'maintenance_status'      => 'nullable',
            'is_remove_image'         => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.required' => __('admin.general_settings.validation_failed'),
        ];
    }
}
