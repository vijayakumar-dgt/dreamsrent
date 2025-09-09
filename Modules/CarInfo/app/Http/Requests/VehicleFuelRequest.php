<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class VehicleFuelRequest extends CustomFailedValidation
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
            'fuel_type' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('car_fuels', 'fuel_type')
                    ->ignore($id)
                    ->whereNull('deleted_at')
                    ->where('language_id', $languageId),
            ],
        ];

    }

    public function messages(): array
    {
        return [
            'fuel_type.required'  => __('admin.rentals.fuel_type_required'),
            'fuel_type.unique'    => __('admin.rentals.fuel_type_unique'),
            'fuel_type.not_regex' => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
