<?php

namespace Modules\CarInfo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CylinderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cylinder_type' => 'required|unique:cylinders,cylinder_type,' . $this->id . ',id,deleted_at,NULL|not_regex:/<\/?script\b[^>]*>/i',
        ];
    }
    public function messages(): array
    {
        return [
            'cylinder_type.required' => __('admin.rentals.cylinder_type_required'),
            'cylinder_type.unique'   => __('admin.rentals.cylinder_type_unique'),
            'cylinder_type.not_regex' => __('admin.common.script_tag_not_allowed'),
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
