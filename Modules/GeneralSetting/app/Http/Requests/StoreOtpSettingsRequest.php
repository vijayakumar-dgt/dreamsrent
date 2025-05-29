<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOtpSettingsRequest extends FormRequest
{
   public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp_type'         => 'required',
            'otp_type.*'       => 'in:sms,email',
            'otp_digit_limit'  => 'required|integer|in:4,5,6',
            'otp_expire_time'  => 'required|string|in:2 mins,5 mins,10 mins',
            'login'            => 'nullable|boolean',
            'register'         => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'otp_type.required'        => __('The OTP type field is required.'),
            'otp_type.*.in'            => __('Invalid OTP type selected.'),
            'otp_digit_limit.required' => __('The OTP digit limit field is required.'),
            'otp_digit_limit.integer'  => __('The OTP digit limit must be a number.'),
            'otp_digit_limit.in'       => __('Invalid OTP digit limit selected.'),
            'otp_expire_time.required' => __('The OTP expiry time field is required.'),
            'otp_expire_time.in'       => __('Invalid OTP expiry time selected.'),
        ];
    }
}
