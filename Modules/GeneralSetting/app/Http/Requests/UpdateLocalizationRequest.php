<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class UpdateLocalizationRequest extends CustomFailedValidation
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'timezone'           => 'required|exists:timezones,id',
            'week_start_day'     => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'date_format'        => 'required|exists:date_formats,id',
            'time_format'        => 'required|exists:time_formats,id',
            'default_language'   => 'nullable|exists:translation_languages,id',
            'currency'           => 'required|exists:currencies,id',
            'currency_symbol'    => 'required|string|max:10',
            'currency_position'  => 'nullable|in:left,right,left_with_space,right_with_space',
            'decimal_seperator'  => 'nullable|in:.,,',
            'thousand_seperator' => 'nullable|in:.,,',
            'currency_switcher'  => 'nullable|in:on',
            'language_switcher'  => 'sometimes|in:on',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'timezone.required'           => __('The timezone field is required.'),
            'timezone.exists'             => __('The selected timezone is invalid.'),
            'week_start_day.required'     => __('The week start day field is required.'),
            'week_start_day.in'           => __('The selected week start day is invalid.'),
            'date_format.required'        => __('The date format field is required.'),
            'date_format.exists'          => __('The selected date format is invalid.'),
            'time_format.required'        => __('The time format field is required.'),
            'time_format.exists'          => __('The selected time format is invalid.'),
            'default_language.exists'     => __('The selected default language is invalid.'),
            'currency.required'           => __('The currency field is required.'),
            'currency.exists'             => __('The selected currency is invalid.'),
            'currency_symbol.required'    => __('The currency symbol field is required.'),
            'currency_position.required'  => __('The currency position field is required.'),
            'currency_position.in'        => __('The selected currency position is invalid.'),
            'decimal_seperator.required'  => __('The decimal separator field is required.'),
            'thousand_seperator.required' => __('The thousand separator field is required.'),
        ];
    }
}
