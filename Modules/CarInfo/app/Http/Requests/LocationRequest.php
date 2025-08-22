<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class LocationRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('locations', 'name')
                    ->ignore($this->id)
                    ->whereNull('deleted_at')
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('locations', 'email')
                    ->ignore($this->id)
                    ->whereNull('deleted_at')
            ],
            'international_phone_number' => [
                'required',
                'numeric',
                Rule::unique('locations', 'phone')
                    ->ignore($this->id)
                    ->whereNull('deleted_at')
            ],
            'address' => 'required',
            'country' => 'required',
            'state'   => 'required',
            'city'    => 'required',
            'pincode' => 'required|max:6',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                       => __('admin.manage.name_required'),
            'name.unique'                         => __('admin.manage.name_unique'),
            'email.required'                      => __('admin.common.email_required'),
            'email.email'                         => __('admin.common.invalid_email'),
            'email.unique'                        => __('admin.common.email_unique'),
            'international_phone_number.required' => __('admin.common.phone_number_required'),
            'international_phone_number.numeric'  => __('admin.common.phone_number_numeric'),
            'international_phone_number.unique'   => __('admin.common.phone_number_unique'),
            'address.required'                    => __('admin.manage.address_required'),
            'country.required'                    => __('admin.manage.country_required'),
            'state.required'                      => __('admin.manage.state_required'),
            'city.required'                       => __('admin.manage.city_required'),
            'pincode.required'                    => __('admin.manage.pincode_required'),
            'pincode.max'                         => __('admin.manage.pincode_max'),
        ];
    }
}
