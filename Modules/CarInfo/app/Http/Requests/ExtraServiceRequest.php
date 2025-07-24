<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class ExtraServiceRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? null;

        return [
            'name' => [
                'required',
                Rule::unique('extra_services')->ignore($id)->whereNull('deleted_at'),
                'max:30',
                'min:3',
                'not_regex:/<\/?script\b[^>]*>/i',
            ],
            'icon' => [
                'mimes:jpeg,jpg,png,svg',
                'max:2048'
            ],
            'image' => [
                'mimes:jpeg,jpg,png,svg',
                'max:2048'
            ],
            'description' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => __('admin.rentals.name_required'),
            'name.unique'           => __('admin.manage.name_unique'),
            'name.max'              => __('admin.rentals.name_maxlength'),
            'name.min'              => __('admin.rentals.name_minlength'),
            'name.not_regex'        => __('admin.common.script_tag_not_allowed'),
            'icon.mimes'            => __('admin.rentals.icon_extension'),
            'icon.max'              => __('admin.rentals.image_size', ['size' => 2]),
            'image.mimes'           => __('admin.rentals.extra_service_image_format'),
            'image.max'             => __('admin.common.image_size', ['size' => 2]),
            'description.required'  => __('admin.rentals.description_required'),
            'description.not_regex' => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
