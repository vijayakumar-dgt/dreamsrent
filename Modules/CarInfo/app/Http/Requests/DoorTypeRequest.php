<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class DoorTypeRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? null;

        return [
            'door_type' => [
                'required',
                'max:1',
                Rule::unique('door_types', 'door_type')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'door_type.required' => __('admin.rentals.door_type_required'),
            'door_type.unique'   => __('admin.rentals.door_type_unique'),
            'door_type.max'      => __('admin.rentals.door_type_maxlength'),
        ];
    }
}
