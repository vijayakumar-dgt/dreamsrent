<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;

class UserLoginRequest extends CustomFailedValidation
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
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => __('web.auth.email_required'),
            'email.email'       => __('web.auth.valid_email'),
            'email.exists'      => __('web.auth.no_account_found'),
            'password.required' => __('web.auth.password_required'),
            'password.min'      => __('web.auth.password_minlength'),
        ];
    }
}
