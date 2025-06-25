<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;

class UserProfileRequest extends CustomFailedValidation
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->id;
        return [
                'id'            => 'required|exists:users,id',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'first_name'    => 'required|string|max:50',
                'last_name'     => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email,' . $userId,
                'user_phone'    => 'required|numeric',
                'address_line'  => 'nullable|string|max:255',
                'country'       => 'required|integer|exists:countries,id',
                'state'         => 'required|integer|exists:states,id',
                'city'          => 'required|integer|exists:cities,id',
                'postal_code'   => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'profile_photo.image' => __('web.auth.valid_profile_img'),
            'profile_photo.mimes' => __('web.auth.profile_img_mimes'),
            'profile_photo.max'   => __('web.auth.profile_max_2mb'),
            'first_name.required' => __('web.auth.first_name_required'),
            'first_name.string'   => __('web.auth.firstname_string'),
            'first_name.max'      => __('web.auth.first_name_maxlength'),
            'last_name.required'  => __('web.auth.last_name_required'),
            'last_name.string'    => __('web.auth.lastname_string'),
            'last_name.max'       => __('web.auth.last_name_maxlength'),
            'email.required'      => __('web.auth.email_required'),
            'email.email'         => __('web.auth.valid_email'),
            'email.unique'        => __('web.auth.email_already_taken'),
            'user_phone.required' => __('web.auth.phone_required'),
            'user_phone.numeric'  => __('web.auth.phone_numeric'),
            'address_line.string' => __('web.auth.address_string'),
            'address_line.max'    => __('web.auth.address_maxlength'),
            'country.required'    => __('web.auth.country_required'),
            'city.required'       => __('web.auth.city_required'),
        ];
    }
}
