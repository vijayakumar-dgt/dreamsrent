<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;

class UserRegisterRequest extends CustomFailedValidation
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
            'first_name' => 'required|regex:/^[A-Za-z]+$/|min:3|max:50',
            'last_name'  => 'required|regex:/^[A-Za-z]+$/|min:3|max:50',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => __('web.auth.email_required'),
            'email.email'       => __('web.auth.valid_email'),
            'email.unique'      => __('web.auth.email_exists'),
            'password.required' => __('web.auth.password_required'),
            'password.min'      => __('web.auth.password_minlength'),
        ];
    }
}
