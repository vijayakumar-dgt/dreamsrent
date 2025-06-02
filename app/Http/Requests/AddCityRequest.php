<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cityId = $this->id;

        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('cities')
                    ->where(fn($query) => $query->where('state_id', $this->state_id))
                    ->ignore($cityId),
            ],
            'state_id' => 'required|exists:states,id',
            'status' => 'nullable|boolean',
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => __('admin.cms.city_required'),
            'name.unique' => __('admin.cms.city_exists'),
            'name.max' => __('admin.cms.city_max_length'),
            'state_id.required' => __('admin.cms.state_required'),
            'state_id.exists' => __('admin.cms.state_exists'),
        ];
    }
}
