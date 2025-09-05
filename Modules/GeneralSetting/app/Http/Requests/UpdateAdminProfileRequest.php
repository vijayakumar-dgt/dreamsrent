<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class UpdateAdminProfileRequest extends CustomFailedValidation
{
    /**
     * Common validation rule for optional numeric fields.
     */
    private const NULLABLE_NUMERIC = 'nullable|numeric';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'            => 'required|exists:users,id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $this->id,
            'phone'         => 'required',
            'address_line'  => 'nullable|string|max:255',
            'postal_code'   => 'nullable|string|max:10',
            'country'       => self::NULLABLE_NUMERIC,
            'state'         => self::NULLABLE_NUMERIC,
            'city'          => self::NULLABLE_NUMERIC,
        ];
    }
}
