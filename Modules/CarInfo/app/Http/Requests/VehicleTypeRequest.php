<?php

namespace Modules\CarInfo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleTypeRequest extends FormRequest
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
                'max:30',
                Rule::unique('cartypes', 'name')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
                'not_regex:/<\/?script\b[^>]*>/i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('admin.rentals.vehicle_type_required'),
            'name.unique'   => __('admin.rentals.vehicle_type_unique'),
            'name.not_regex'=> __('admin.common.script_tag_not_allowed'),
        ];
    }
}
