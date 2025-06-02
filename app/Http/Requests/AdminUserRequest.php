<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'first_name' => ['required', 'min:3', 'max:20'],
            'last_name' => ['required', 'max:20'],
            'phone_number' => ['required'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'image' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'role_id' => ['required'],
            'password' => [$id ? 'nullable' : 'required'],
            'confirm_password' => [$id ? 'nullable' : 'required', 'same:password'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => __('admin.common.first_name_required'),
            'first_name.min' => __('admin.common.first_name_minlength', ['min' => 3]),
            'first_name.max' => __('admin.common.first_name_maxlength', ['max' => 30]),
            'last_name.required' => __('admin.common.last_name_required'),
            'last_name.min' => __('admin.common.last_name_minlength', ['min' => 3]),
            'last_name.max' => __('admin.common.last_name_maxlength', ['max' => 30]),
            'username.required' => __('admin.common.username_required'),
            'username.max' => __('admin.common.username_maxlength'),
            'username.unique' => __('admin.common.username_unique'),
            'phone_number.required' => __('admin.common.phone_number_required'),
            'image.mimes' => __('admin.common.image_format'),
            'image.max' => __('admin.common.image_size', ['size' => 2]),
            'email.required' => __('admin.common.email_required'),
            'email.email' => __('admin.common.email_valid'),
            'email.unique' => __('admin.common.email_unique'),
            'role_id.required' => __('admin.user_management.role_required'),
            'password.required' => __('admin.common.password_required'),
            'confirm_password.required' => __('admin.common.confirm_password_required'),
            'confirm_password.same' => __('admin.common.confirm_password_equal_to'),
        ];
    }
}
