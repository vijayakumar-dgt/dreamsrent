<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class EditStateRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->input('id');
        
        return [
            'id' => 'required|exists:states,id',
            'name' => [
                'required',
                'max:255',
                Rule::unique('states')->where(function ($query) {
                    return $query->where('country_id', $this->country_id);
                })->ignore($id)
            ],
            'country_id' => 'required|exists:countries,id',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => __('admin.common.id_required'),
            'id.exists' => __('admin.common.id_not_exists'),
            'name.required' => __('admin.cms.state_required'),
            'name.unique' => __('admin.cms.state_exists'),
            'name.max' => __('admin.cms.state_max_length'),
            'country_id.required' => __('admin.cms.country_required'),
            'country_id.exists' => __('admin.cms.country_exists'),
        ];
    }
}
