<?php

namespace Modules\CarInfo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SafetyFeatureRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->id ?? null;

        return [
            'feature' => [
                'required',
                'max:100',
                'min:3',
                Rule::unique('safety_features')->ignore($id)->whereNull('deleted_at'),
                'not_regex:/<\/?script\b[^>]*>/i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'feature.required' => __('admin.rentals.feature_required'),
            'feature.max' => __('admin.rentals.feature_maxlength'),
            'feature.min' => __('admin.rentals.feature_minlength'),
            'feature.unique' => __('admin.rentals.feature_unique'),
            'feature.not_regex' => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
