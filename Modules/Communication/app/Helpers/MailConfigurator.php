<?php

namespace Modules\Communication\Helpers;

use Illuminate\Support\Facades\Config;
use Modules\Communication\Exceptions\SendGridConfigurationException;
use Modules\Communication\Exceptions\SmtpConfigurationException;
use Modules\GeneralSetting\Models\CommunicationSetting;
use Modules\GeneralSetting\Models\GeneralSetting;

class MailConfigurator
{
    /**
     * Configure mail settings based on communication settings.
     * @throws SmtpConfigurationException
     * @throws SendGridConfigurationException
     */
    public static function configureMail(): void
    {
        /** @var CommunicationSetting|null $settings */
        $settings = CommunicationSetting::where('settings_type', 1)
            ->whereIn('key', ['phpmail_status', 'smtp_status', 'sendgrid_status'])
            ->where('value', 1)
            ->first();

        if (!$settings) {
            return;
        }

        if ($settings->key === 'phpmail_status') {
            self::configurePhpMail();
        } elseif ($settings->key === 'smtp_status') {
            self::configureSmtpMail();
        } elseif ($settings->key === 'sendgrid_status') {
            self::configureSendGrid();
        }
    }

    private static function configurePhpMail(): void
    {
        $phpmail = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'phpmail')
            ->where('key', 'phpmail_from_email')
            ->value('value');

        $phpusername = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'phpmail')
            ->where('key', 'phpmail_from_name')
            ->value('value');

        if ($phpmail) {
            Config::set('mail.from.address', $phpmail);
            Config::set('mail.from.name', $phpusername ?? 'No-Reply');
            Config::set('mail.default', 'mail');
            Config::set('mail.mailers.mail', ['transport' => 'mail']);
        }
    }

    private static function configureSmtpMail(): void
    {
        $getmail = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'smtp')
            ->where('key', 'smtp_from_email')
            ->value('value');

        $getpassword = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'smtp')
            ->where('key', 'smtp_password')
            ->value('value');

        $getusername = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'smtp')
            ->where('key', 'smtp_from_name')
            ->value('value');

        $gethost = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'smtp')
            ->where('key', 'smtp_host')
            ->value('value');

        $getPort = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'smtp')
            ->where('key', 'smtp_port')
            ->value('value');


         if (!$getmail || !$getpassword || !$gethost) {
            throw new SmtpConfigurationException("SMTP settings are incomplete.");
        }

        Config::set('mail.from.address', $getmail);
        Config::set('mail.from.name', $getusername ?? "No-Reply");
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport'  => 'smtp',
            'host'       => $gethost,
            'port'       => $getPort ?? 587,
            'encryption' => 'tls',
            'username'   => $getmail,
            'password'   => $getpassword,
        ]);
    }

    private static function configureSendGrid(): void
    {
        $getmail = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'sendgrid')
            ->where('key', 'sendgrid_from_email')
            ->value('value');

        $getkey = CommunicationSetting::where('settings_type', 1)
            ->where('type', 'sendgrid')
            ->where('key', 'sendgrid_key')
            ->value('value');

        $companyName = GeneralSetting::where('key', 'organization_name')->first();

          if (!$getmail || !$getkey) {
            throw new SendGridConfigurationException("SendGrid settings are incomplete.");
        }

        Config::set('mail.from.address', $getmail);
        Config::set('mail.from.name', $companyName->value ?? "No-Reply");
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport'  => 'smtp',
            'host'       => 'smtp.sendgrid.net',
            'port'       => 587,
            'encryption' => 'tls',
            'username'   => 'apikey',
            'password'   => $getkey,
        ]);
    }
}
