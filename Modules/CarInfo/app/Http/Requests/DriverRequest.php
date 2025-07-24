<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class DriverRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->id;

        return [
            'driver_name'  => ['required', 'max:20'],
            'gender'       => ['required'],
            'phone_number' => ['required'],
            'email'        => [
                'required',
                'email',
                Rule::unique('drivers', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'address'     => ['required', 'max:150'],
            'card_number' => [
                'required',
                Rule::unique('drivers', 'card_number')->ignore($id)->whereNull('deleted_at'),
            ],
            'image'         => 'mimes:jpeg,jpg,png|max:2048',
            'date_of_issue' => ['required'],
            'valid_date'    => ['required'],
            'documents.*'   => 'file|mimes:jpeg,jpg,png,pdf,doc,docx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'driver_name.required'   => __('admin.manage.driver_name_required'),
            'driver_name.max'        => __('admin.manage.driver_name_maxlength'),
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
