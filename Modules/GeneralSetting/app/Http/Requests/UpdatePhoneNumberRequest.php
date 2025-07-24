<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class UpdatePhoneNumberRequest extends CustomFailedValidation
{
    public function rules(): array
    {
        return [
           'new_phonenumber'        => 'required|unique:users,phone_number',
           'current_phonenumber'    => 'required',
           'phone_current_password' => 'required',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
