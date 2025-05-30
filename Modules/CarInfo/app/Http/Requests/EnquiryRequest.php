<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;

class EnquiryRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assigned_cars'     => 'required|array',
            'assigned_cars.*'   => 'exists:vehicle_info,id',
            'customer_name'     => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email'             => 'required|email|max:100',
            'phone_number'      => 'required|digits_between:10,15',
            'enquiry_details'   => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_cars.required' => 'Please select at least one car.',
            'assigned_cars.*.exists' => 'One or more selected cars are invalid.',
            'customer_name.required' => 'Customer name is required.',
            'customer_name.regex'    => 'Customer name can only contain letters and spaces.',
            'email.required'         => 'Email is required.',
            'email.email'            => 'Email format is invalid.',
            'phone_number.required'  => 'Phone number is required.',
            'phone_number.digits_between' => 'Phone number must be between 10 and 15 digits.',
            'enquiry_details.required' => 'Enquiry details are required.',
            'enquiry_details.max'    => 'Enquiry details may not be greater than 500 characters.',
        ];
    }

}
