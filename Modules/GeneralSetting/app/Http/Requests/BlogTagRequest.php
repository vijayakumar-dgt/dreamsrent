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
        $id = $this->route('id');
        return [
            'name' => 'required|string|max:255|unique:blog_tags,name,' . $id,
            'language_id' => 'required|integer',
            'status' => 'nullable|boolean',
        ];
    }
}
