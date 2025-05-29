<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleColorRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;
        $languageId = current_user()?->language_id;

        return [
            'name' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('car_colors', 'name')
                    ->ignore($id)
                    ->whereNull('deleted_at')
                    ->where('language_id', $languageId)
            ],
            'value' => [
                'required',
                Rule::unique('car_colors', 'value')
                    ->ignore($id)
                    ->whereNull('deleted_at')
                    ->where('language_id', $languageId)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('admin.rentals.color_name_required'),
            'value.required' => __('admin.rentals.color_code_required'),
            'name.not_regex' => __('admin.common.script_tag_not_allowed'),
            'value.unique' => __('admin.rentals.color_code_unique'),
            'name.unique' => __('admin.rentals.color_name_unique'),
        ];
    }
}
