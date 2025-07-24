<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;

class ResetPasswordRequest extends CustomFailedValidation
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'            => 'required|email|exists:users,email',
            'current_password' => 'required|string|min:6',
            'confirm_password' => 'required|same:current_password',
        ];
    }

    public function messages()
    {
        return [
            'email.required'            => __('web.home.email_required'),
            'email.email'               => __('web.home.valid_email'),
            'email.exists'              => __('web.auth.email_already_taken'),
            'current_password.required' => __('web.user.current_password_required'),
            'current_password.min'      => __('web.user.password_length_must_6')
        ];
    }
}
