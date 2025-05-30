<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? null;

        return [
            'tag' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('tags', 'tag')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tag.required'   => __('admin.rentals.tag_required'),
            'tag.unique'     => __('admin.rentals.tag_unique'),
            'tag.not_regex'  => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
