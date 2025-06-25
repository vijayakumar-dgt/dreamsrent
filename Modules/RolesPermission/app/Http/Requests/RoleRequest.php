<?php

namespace Modules\RolesPermission\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class RoleRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id');
        $authId = current_user()->id;

        return [
            'role' => [
                'required',
                'min:3',
                'max:30',
                Rule::unique('roles', 'role_name')
                    ->ignore($id)
                    ->where(function ($query) use ($authId) {
                        $query->whereNull('deleted_at')->where('created_by', $authId);
                    }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => __('admin.user_management.role_required'),
            'role.max'      => __('admin.user_management.role_maxlength'),
            'role.min'      => __('admin.user_management.role_minlength'),
            'role.unique'   => __('admin.user_management.role_unique'),
        ];
    }
}
