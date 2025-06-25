<?php

namespace Modules\Installer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string',
            'email'            => 'required|email',
            'password'         => 'required|string|same:confirm_password|min:8',
            'confirm_password' => 'required|string|min:8',
        ];
    }
}
