<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;


class CompanySettingRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true; // Adjust if needed for permissions
    }

    public function rules(): array
    {
        return [
            'organization_name' => 'required|string|max:100',
            'owner_name' => 'required|string|max:100',
            'company_email' => 'required|email|max:100',
            'company_phone' => 'required',
            'international_phone_number' => 'required',
            'company_address_line' => 'nullable|string|max:150',
            'company_postal_code' => 'nullable|string|max:10',
            'company_profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'group_id' => 'nullable|integer',
            'country' => 'nullable',
            'state' => 'nullable',
            'city' => 'nullable',
        ];
    }
}
