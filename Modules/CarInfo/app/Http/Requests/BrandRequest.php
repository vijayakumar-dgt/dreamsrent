<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? '';
        $language_id = current_user()?->language_id;

        return [
            'brand_name' => [
                'required',
                'max:30',
                'min:3',
                Rule::unique('brands')
                    ->ignore($id)
                    ->whereNull('deleted_at')
                    ->where('language_id', $language_id),
                'not_regex:/<\/?script\b[^>]*>/i'
            ],
            'brand_image' => 'nullable|mimes:jpeg,jpg,png,svg|max:2048',
            'brand_icon' => 'nullable|mimes:jpeg,jpg,png,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'brand_name.required' => __('admin.rentals.brand_name_required'),
            'brand_name.max' => __('admin.rentals.brand_name_maxlength'),
            'brand_name.min' => __('admin.rentals.brand_name_minlength'),
            'brand_name.unique' => __('admin.rentals.brand_name_unique'),
            'brand_name.not_regex' => __('admin.common.script_tag_not_allowed'),
            'brand_image.mimes' => __('admin.rentals.brand_image_format'),
            'brand_image.max' => __('admin.rentals.brand_image_size'),
        ];
    }
}
