<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class UpdateEmailRequest extends CustomFailedValidation
{
    public function rules(): array
    {
        return [
            'new_email' => 'required|email|unique:users,email',
            'current_email' => 'required|email',
            'email_current_password' => 'required',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
