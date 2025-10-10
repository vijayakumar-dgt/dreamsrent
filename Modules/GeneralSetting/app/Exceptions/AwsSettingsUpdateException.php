<?php
namespace Modules\GeneralSetting\Exceptions;

use Exception;

class AwsSettingsUpdateException extends Exception
{
    public function __construct($message = "AWS settings update failed", $code = 500)
    {
        parent::__construct($message, $code);
    }
}
