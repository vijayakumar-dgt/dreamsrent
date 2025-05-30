<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleSeatRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? '';

        return [
            'seat_type' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('seat_types', 'seat_type')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'seat_type.required'   => __('admin.rentals.seat_type_required'),
            'seat_type.unique'     => __('admin.rentals.seat_type_unique'),
            'seat_type.not_regex'  => __('admin.common.script_tag_not_allowed'),
        ];
    }

}
