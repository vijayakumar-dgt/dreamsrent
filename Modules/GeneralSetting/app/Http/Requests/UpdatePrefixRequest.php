<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class UpdatePrefixRequest extends CustomFailedValidation
{
    /**
     * A reusable validation rule for all prefix fields.
     */
    private const PREFIX_RULE = 'required|string|max:20';

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
            'group_id'           => 'required|integer',
            'reservation_prefix' => self::PREFIX_RULE,
            'quotation_prefix'   => self::PREFIX_RULE,
            'enquiry_prefix'     => self::PREFIX_RULE,
            'company_prefix'     => self::PREFIX_RULE,
            'inspection_prefix'  => self::PREFIX_RULE,
            'report_prefix'      => self::PREFIX_RULE,
            'customer_prefix'    => self::PREFIX_RULE,
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
            'group_id.required' => __('The group ID is required.'),
            'group_id.integer'  => __('The group ID must be an integer.'),
            '*.required'        => __('The :attribute field is required.'),
            '*.string'          => __('The :attribute must be a string.'),
            '*.max'             => __('The :attribute may not be greater than :max characters.'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'reservation_prefix' => 'reservation prefix',
            'quotation_prefix'   => 'quotation prefix',
            'enquiry_prefix'     => 'enquiry prefix',
            'company_prefix'     => 'company prefix',
            'inspection_prefix'  => 'inspection prefix',
            'report_prefix'      => 'report prefix',
            'customer_prefix'    => 'customer prefix',
        ];
    }
}
