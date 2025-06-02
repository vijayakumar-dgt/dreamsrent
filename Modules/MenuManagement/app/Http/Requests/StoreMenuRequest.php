<?php

namespace Modules\MenuManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'menu_name' => 'required|string|max:255',
            'menu_type' => 'required|in:header,footer',
            'menu_permalink' => 'required|url|max:255',
            'language' => 'required|integer|exists:translation_languages,id',
        ];
    }

    public function messages()
    {
        return [
            'menu_name.required' => __('Menu name is required'),
            'menu_type.required' => __('Menu type is required'),
            'menu_permalink.required' => __('Permalink is required'),
            'menu_permalink.url' => __('Permalink must be a valid URL'),
            'language.required' => __('Language is required'),
        ];
    }
}
