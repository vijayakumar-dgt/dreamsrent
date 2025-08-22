<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title'       => 'required|string|max:255',
            'language'    => 'required|integer',
            'category_id' => 'required|integer',
            'tag_id'      => 'required|array',
            'description' => 'nullable|string',
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = 'required|image|max:5120'; // 5MB
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['image'] = 'nullable|image|max:5120';
        }

        return $rules;
    }
}
