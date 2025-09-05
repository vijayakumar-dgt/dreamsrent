<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class CommunicationSettingRequest extends CustomFailedValidation
{
    /**
     * Common rule reused for string-based required fields.
     */
    private const REQUIRED_STRING = 'required|string';

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
                'status'  => 'required|in:0,1',
            ];
        }

        if (
            $this->routeIs('admin.email-settings-store') ||
            $this->routeIs('admin.smsstore-settings')
        ) {
            $type  = $this->input('type');
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
            'email_address.email'    => __('admin.common.email_valid'),
        ];
    }

    private function getTypeSpecificRules(string $type): array
    {
        return match ($type) {
            'nexmo' => [
                'nexmo_api_key'    => self::REQUIRED_STRING,
                'nexmo_secret_key' => self::REQUIRED_STRING,
                'nexmo_sender_id'  => self::REQUIRED_STRING,
            ],
            'twofactor' => [
                'twofactor_api_key'    => self::REQUIRED_STRING,
                'twofactor_secret_key' => self::REQUIRED_STRING,
                'twofactor_sender_id'  => self::REQUIRED_STRING,
            ],
            'twilio' => [
                'twilio_api_key'    => self::REQUIRED_STRING,
                'twilio_secret_key' => self::REQUIRED_STRING,
                'twilio_sender_id'  => self::REQUIRED_STRING,
            ],
            'smtp' => [
                'smtp_from_email' => self::REQUIRED_STRING,
                'smtp_password'   => self::REQUIRED_STRING,
                'smtp_from_name'  => self::REQUIRED_STRING,
                'smtp_port'       => 'required',
                'smtp_host'       => 'required',
            ],
            'phpmail' => [
                'phpmail_from_email' => self::REQUIRED_STRING,
                'phpmail_password'   => self::REQUIRED_STRING,
                'phpmail_from_name'  => self::REQUIRED_STRING,
            ],
            'sendgrid' => [
                'sendgrid_from_email' => self::REQUIRED_STRING,
                'sendgrid_key'        => self::REQUIRED_STRING,
            ],
            'fcm' => [
                'project_id'   => self::REQUIRED_STRING,
                'client_email' => self::REQUIRED_STRING,
                'private_key'  => self::REQUIRED_STRING,
            ],
            default => [],
        };
    }
}
