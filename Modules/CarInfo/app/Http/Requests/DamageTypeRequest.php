<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DamageTypeRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? null;

        return [
            'damage_type' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('damage_types', 'damage_type')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'damage_type.required'   => __('admin.rentals.damage_type_required'),
            'damage_type.unique'     => __('admin.rentals.damage_type_unique'),
            'damage_type.not_regex'  => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
