<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleModelRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? '';
        $languageId = current_user()?->language_id;

        return [
            'model_name' => [
                'required',
                'max:30',
                'min:3',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('car_models', 'model_name')
                    ->ignore($id)
                    ->whereNull('deleted_at')
                    ->where('language_id', $languageId),
            ],
            'brand_id' => 'required',
            'total_cars' => [
                'nullable',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'model_name.required' => __('admin.rentals.model_name_required'),
            'model_name.max' => __('admin.rentals.model_name_maxlength'),
            'model_name.min' => __('admin.rentals.model_name_minlength'),
            'model_name.unique' => __('admin.rentals.model_name_unique'),
            'model_name.not_regex' => __('admin.common.script_tag_not_allowed'),
            'brand_id.required' => __('admin.rentals.brand_required'),
            'total_cars.required' => __('admin.rentals.total_vehicles_required'),
        ];
    }

}
