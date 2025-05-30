<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;


class SendNewsLetterRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('admin.common.email_required'),
            'email.email' => __('admin.common.email_invalid'),
        ];
    }
}
