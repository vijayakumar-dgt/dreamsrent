<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterRequest extends CustomFailedValidation
{
  
    public function authorize(): bool
    {
        return true;
    }

      public function rules(): array
    {
        return [
            'subscriber_email' => [
                'required',
                Rule::unique('newsletter_subscribers', 'email')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'subscriber_email.required' => __('web.user.subscriber_email_required'),
            'subscriber_email.unique' => __('web.user.subscriber_email_unique'),
        ];
    }
}
