<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class CustomerRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? '';

        return [
            'first_name'   => ['required', 'min:3', 'max:20'],
            'last_name'    => ['required', 'max:20'],
            'gender'       => ['required'],
            'phone_number' => ['required'],
            'email'        => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'address'     => ['max:150'],
            'card_number' => [
                'required',
                Rule::unique('user_details', 'card_number')->ignore($id, 'user_id')->whereNull('deleted_at'),
            ],
            'image'         => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'date_of_issue' => ['required'],
            'valid_date'    => ['required'],
            'documents.*'   => 'file|mimes:jpeg,jpg,png,pdf,doc,docx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'    => __('admin.common.first_name_required'),
            'first_name.min'         => __('admin.common.first_name_minlength', ['min' => 3]),
            'first_name.max'         => __('admin.common.first_name_maxlength', ['max' => 30]),
            'last_name.required'     => __('admin.common.last_name_required'),
            'last_name.min'          => __('admin.common.last_name_minlength', ['min' => 3]),
            'last_name.max'          => __('admin.common.last_name_maxlength', ['max' => 30]),
            'username.required'      => __('admin.common.username_required'),
            'username.max'           => __('admin.common.username_maxlength'),
            'username.unique'        => __('admin.common.username_unique'),
            'gender.required'        => __('admin.manage.gender_required'),
            'phone_number.required'  => __('admin.common.phone_number_required'),
            'address.required'       => __('admin.manage.address_required'),
            'address.max'            => __('admin.manage.address_maxlength'),
            'image.mimes'            => __('admin.common.image_format'),
            'image.max'              => __('admin.common.image_size', ['size' => 2]),
            'documents.*.mimes'      => __('admin.manage.documents_format'),
            'documents.*.max'        => __('admin.manage.documents_size', ['size' => 5]),
            'card_number.required'   => __('admin.manage.card_number_required'),
            'card_number.unique'     => __('admin.manage.card_number_unique'),
            'date_of_issue.required' => __('admin.manage.date_of_issue_required'),
            'valid_date.required'    => __('admin.manage.valid_date_required'),
            'email.required'         => __('admin.common.email_required'),
            'email.email'            => __('admin.common.email_valid'),
            'email.unique'           => __('admin.common.email_unique'),
        ];
    }
}
