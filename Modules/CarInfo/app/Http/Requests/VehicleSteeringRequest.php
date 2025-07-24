<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class VehicleSteeringRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? null;

        return [
            'steering_type' => [
                'required',
                'max:30',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('car_steerings', 'steering_type')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'steering_type.required'    => __('admin.rentals.steering_type_required'),
            'steering_type.unique'      => __('admin.rentals.steering_type_unique'),
            'steering_type.max'         => __('admin.rentals.steering_type_maxlength'),
            'steering_type.not_regex'   => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
