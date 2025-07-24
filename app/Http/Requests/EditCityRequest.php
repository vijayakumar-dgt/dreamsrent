<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->input('id');

        return [
            'id'   => 'required|exists:cities,id',
            'name' => [
                'required',
                'max:255',
                Rule::unique('cities')->where(function ($query) {
                    return $query->where('state_id', $this->state_id);
                })->ignore($id)
            ],
            'state_id' => 'required|exists:states,id',
            'status'   => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'       => __('admin.common.id_required'),
            'id.exists'         => __('admin.common.id_not_exists'),
            'name.required'     => __('admin.cms.city_required'),
            'name.unique'       => __('admin.cms.city_exists'),
            'name.max'          => __('admin.cms.city_max_length'),
            'state_id.required' => __('admin.cms.state_required'),
            'state_id.exists'   => __('admin.cms.state_exists'),
        ];
    }
}
