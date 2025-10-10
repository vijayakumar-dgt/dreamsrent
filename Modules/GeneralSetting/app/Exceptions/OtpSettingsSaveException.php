<?php
namespace Modules\GeneralSetting\Exceptions;

use Exception;

class OtpSettingsSaveException extends Exception
{
    public function __construct($message = "Failed to save OTP settings", $code = 500)
    {
        parent::__construct($message, $code);
    }
}
