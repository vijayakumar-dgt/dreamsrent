<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class InsuranceRequest extends CustomFailedValidation
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
        $id = $this->id ?? '';

        return [
            'insurance_name' => [
                'required',
                'max:255',
                Rule::unique('insurances')->ignore($id)->whereNull('deleted_at'),
            ],
            'price_type_id' => 'required|exists:pricing_types,id',
            'price'         => 'required|numeric|min:0',
            'benefit.*'     => 'required|string|max:255',
            'status'        => 'sometimes',
            'language_id'   => 'sometimes'
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
            'insurance_name.required' => __('admin.general_settings.insurance_name_required'),
            'insurance_name.unique'   => __('admin.general_settings.insurance_name_exist'),
            'price_type_id.required'  => __('admin.general_settings.price_type_required'),
            'price_type_id.exists'    => __('admin.general_settings.price_type_invalid'),
            'price.required'          => __('admin.general_settings.price_required'),
            'price.numeric'           => __('admin.general_settings.price_numeric'),
            'price.min'               => __('admin.general_settings.price_min'),
            'benefit.*.required'      => __('admin.general_settings.benefit_required'),
            'benefit.*.max'           => __('admin.general_settings.benefit_max'),
        ];
    }
}
