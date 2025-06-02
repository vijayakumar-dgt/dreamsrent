<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;


class CommunicationSettingRequest extends CustomFailedValidation
{
    public function authorize(): bool  
    {
        return true;
    }

    public function rules(): array
    {
       $rules = [
        'type' => 'required|string|in:nexmo,twilio,twofactor,phpmail,smtp,sendgrid,fcm',
    ];
        if ($this->routeIs('admin.statusUpdate-settings')) {
            $rules = [
                'gateway' => 'required|in:nexmo,twilio,twofactor,phpmail,smtp,sendgrid',
                'status' => 'required|in:0,1',
            ];
        }

         if (
            $this->routeIs('admin.email-settings-store') ||
            $this->routeIs('admin.smsstore-settings')  
        )  {
            
            $type = $this->input('type');
            $rules = array_merge($rules, $this->getTypeSpecificRules($type));
        }

        if ($this->routeIs('admin.send-test-mail')) {
            $rules = [
                'email_address' => 'required|email',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email_address.required' => __('admin.general_settings.email_address_required'),
            'email_address.email' => __('admin.common.email_valid'),
        ];
    }

    private function getTypeSpecificRules(string $type): array
    {
        return match ($type) {
            'nexmo' => [
                'nexmo_api_key' => 'required|string',
                'nexmo_secret_key' => 'required|string',
                'nexmo_sender_id' => 'required|string',
            ],
            'twofactor' => [
                'twofactor_api_key' => 'required|string',
                'twofactor_secret_key' => 'required|string',
                'twofactor_sender_id' => 'required|string',
            ],
            'twilio' => [
                'twilio_api_key' => 'required|string',
                'twilio_secret_key' => 'required|string',
                'twilio_sender_id' => 'required|string',
            ],
            'smtp' => [
                'smtp_from_email' => 'required|string',
                'smtp_password' => 'required|string',
                'smtp_from_name' => 'required|string',
                'smtp_port' => 'required',
                'smtp_host' => 'required',
            ],
            'phpmail' => [
                'phpmail_from_email' => 'required|string',
                'phpmail_password' => 'required|string',
                'phpmail_from_name' => 'required|string',
            ],
            'sendgrid' => [
                'sendgrid_from_email' => 'required|string',
                'sendgrid_key' => 'required|string',
            ],
            'fcm' => [
                'project_id' => 'required|string',
                'client_email' => 'required|string',
                'private_key' => 'required|string',
            ],
            default => [],
        };
    }
}
