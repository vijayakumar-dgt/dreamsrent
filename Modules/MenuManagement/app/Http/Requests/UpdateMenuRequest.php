<?php

namespace Modules\MenuManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'menu_id' => 'required|exists:menus,id',
            'editMenuType' => 'required|in:header,footer',
            'editMenuName' => 'required|string|min:3|max:255',
            'editMenuPermalink' => 'required|url|max:255',
            'menu_status' => 'nullable|in:on,off',
            'language' => 'required|integer|exists:translation_languages,id',
        ];
    }

    public function messages()
    {
        return [
            'menu_id.required' => __('Menu ID is required'),
            'editMenuName.required' => __('Menu name is required'),
            'editMenuPermalink.required' => __('Permalink is required'),
            'editMenuPermalink.url' => __('Permalink must be a valid URL'),
            'language.required' => __('Language is required'),
        ];
    }
}
