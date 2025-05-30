<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;


class SendNewsLetterRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('admin.common.email_required')
        ];
    }
}
