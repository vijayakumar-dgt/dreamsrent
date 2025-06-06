<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class SeasonRequest extends CustomFailedValidation
{
    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'name' => [
                'required',
                'max:30',
                Rule::unique('seasons')->ignore($id)->whereNull('deleted_at'),
                'not_regex:/<\/?script\b[^>]*>/i',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => __('admin.rentals.season_name_required'),
            'name.unique' => __('admin.rentals.season_name_unique'),
            'name.max' => __('admin.rentals.season_name_maxlength'),
            'name.not_regex' => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
