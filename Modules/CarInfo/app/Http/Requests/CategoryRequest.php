<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class CategoryRequest extends CustomFailedValidation
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->input('id'); // Retrieve the category ID from the route (if present)

        return [
            'name' => [
                'required',
                Rule::unique('categories', 'name')->ignore($id)->whereNull('deleted_at'),
                'not_regex:/<\/?script\b[^>]*>/i',
            ],
        ];
    }

    public function message(): array
    {
        return [
            'name.required'  => __('admin.rentals.category_required'),
            'name.unique'    => __('admin.rentals.category_unique'),
            'name.not_regex' => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
