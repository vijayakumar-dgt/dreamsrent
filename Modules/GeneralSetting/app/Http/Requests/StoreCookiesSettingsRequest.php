<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCookiesSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => 'required|integer',
            'language' => 'required|integer',
            'cookiesContentText' => 'required|string|max:5000',
            'cookiesPosition' => 'required|in:right,left',
            'agreeButtonText' => 'required|string|min:2|max:255',
            'declineButtonText' => 'required|string|min:2|max:255',
            'showDeclineButton' => 'nullable|boolean',
            'cookiesPageLink' => 'required|url|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.required' => __('The group ID is required.'),
            'language.required' => __('The language ID is required.'),
            'cookiesContentText.required' => __('Cookies content is required.'),
            'cookiesPosition.in' => __('Position must be either "right" or "left".'),
            'agreeButtonText.required' => __('Agree button text is required.'),
            'declineButtonText.required' => __('Decline button text is required.'),
            'cookiesPageLink.url' => __('The cookies page link must be a valid URL.'),
        ];
    }
}
