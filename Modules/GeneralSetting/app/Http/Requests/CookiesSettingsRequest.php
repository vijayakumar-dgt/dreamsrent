<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CookiesSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'group_id' => 'required|integer',
            'language_id' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'group_id.required' => __('The group ID is required.'),
            'group_id.integer' => __('The group ID must be an integer.'),
            'language_id.integer' => __('The language ID must be an integer.'),
        ];
    }
}
