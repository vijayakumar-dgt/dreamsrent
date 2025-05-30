<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;
class UpdateAdminProfileRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:users,id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->id,
            'phone' => 'required',
            'address_line' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|numeric',
            'state' => 'nullable|numeric',
            'city' => 'nullable|numeric',
        ];
    }
}
