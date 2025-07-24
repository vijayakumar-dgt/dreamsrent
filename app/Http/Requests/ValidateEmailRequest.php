<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;

class ValidateEmailRequest extends CustomFailedValidation
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
            'email' => 'required|email',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('web.auth.email_required'),
            'email.email'    => __('web.auth.valid_email'),
        ];
    }
}
