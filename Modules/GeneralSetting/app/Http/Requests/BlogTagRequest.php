<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:blog_tags,name',
            'language_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('admin.manage.name_required'),
            'name.max'      => __('admin.manage.name_maxlength'),
            'name.min'      => __('admin.manage.name_minlength'),
            'name.unique'   => __('admin.manage.name_unique'),
        ];
    }
}
