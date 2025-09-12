<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('admin.general_settings.enter_current_password'),
            'new_password.required'     => __('admin.general_settings.enter_new_password'),
            'new_password.min'          => __('admin.general_settings.password_character'),
            'confirm_password.required' => __('admin.general_settings.enter_confirm_password'),
            'confirm_password.same'     => __('admin.general_settings.confirm_password_match'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = currentUser();

            if ($user && $user->password !== null && !Hash::check($this->current_password, $user->password)) {
                $validator->errors()->add('current_password', __('admin.general_settings.current_password_incorrect'));
            }
        });
    }
}
