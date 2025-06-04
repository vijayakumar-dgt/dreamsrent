<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $countryId = $this->input('id');

        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('countries')->ignore($countryId),
            ],
            'code' => 'nullable|max:50',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('admin.cms.country_required'),
            'name.unique' => __('admin.cms.country_exists'),
            'name.max' => __('admin.cms.country_max_length'),
        ];
    }
}
