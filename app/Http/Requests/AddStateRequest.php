<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $stateId = $this->id;
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('states')
                    ->where(fn($query) => $query->where('country_id', $this->country_id))
                    ->ignore($stateId),
            ],
            'country_id' => 'required|exists:countries,id',
            'status' => 'nullable|boolean',
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => __('admin.cms.state_required'),
            'name.unique' => __('admin.cms.state_exists'),
            'name.max' => __('admin.cms.state_max_length'),
            'country_id.required' => __('admin.cms.country_required'),
            'country_id.exists' => __('admin.cms.country_exists'),
        ];
    }
}
