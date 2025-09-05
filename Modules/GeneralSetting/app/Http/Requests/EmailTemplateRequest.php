<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class EmailTemplateRequest extends CustomFailedValidation
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'             => 'required|string|max:255',
            'notification_type' => [
                'required',
                $this->uniqueNotificationTypeRule()
            ],
            'subject'              => 'required|string|max:255',
            'sms_content'          => 'required|string|max:500',
            'notification_content' => 'nullable|string',
            'description'          => 'required|string',
            'status'               => 'sometimes'
        ];
    }

    protected function uniqueNotificationTypeRule()
    {
        return Rule::unique('email_templates', 'notification_type')
            ->whereNull('deleted_at')
            ->ignore($this->id);
    }

    public function messages()
    {
        return [
            'title.required'             => __('The title field is required.'),
            'notification_type.required' => __('The notification type is required.'),
            'notification_type.unique'   => __('An email template already exists for this notification type.'),
            'subject.required'           => __('The subject field is required.'),
            'sms_content.required'       => __('The SMS content is required.'),
            'description.required'       => __('The description cannot be empty.'),
            '*.max'                      => __('The :attribute may not be greater than :max characters.'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $description = strip_tags(trim($this->description));
            if (empty($description)) {
                $validator->errors()->add('description', __('The description cannot be empty.'));
            }
        });
    }
}
