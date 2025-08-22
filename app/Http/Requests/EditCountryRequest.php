<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $this->route('id') ?? $this->input('id');

        return [
            'id'   => 'required|exists:countries,id',
            'name' => [
                'required',
                'max:255',

            ],
            'code'   => 'nullable|max:50',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'   => __('admin.common.id_required'),
            'id.exists'     => __('admin.common.id_not_exists'),
            'name.required' => __('admin.cms.country_required'),
            'name.unique'   => __('admin.cms.country_exists'),
            'name.max'      => __('admin.cms.country_max_length'),
        ];
    }
}
