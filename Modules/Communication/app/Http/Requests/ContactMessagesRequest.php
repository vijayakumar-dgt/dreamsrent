<?php

namespace Modules\Communication\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class ContactMessagesRequest extends CustomFailedValidation
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone_number' => 'required|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('admin.support.name_required'),
            'email.required' => __('admin.support.email_required'),
            'email.unique' => __('admin.support.email_unique'),
            'email.email' => __('admin.support.email_invalid'),
            'phone_number.required' => __('admin.support.phone_required'),
            'message.required' => __('admin.support.message_required'),
            'image.image' => __('admin.support.image_must_be_image'),
            'image.mimes' => __('admin.support.image_mime_invalid'),
            'image.max' => __('admin.support.image_size_exceeded'),
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
