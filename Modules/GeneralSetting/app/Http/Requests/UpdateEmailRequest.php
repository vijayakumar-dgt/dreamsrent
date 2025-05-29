<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRequest extends FormRequest
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
