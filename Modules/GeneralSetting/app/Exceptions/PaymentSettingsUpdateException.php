<?php
namespace Modules\GeneralSetting\Exceptions;

use Exception;

class PaymentSettingsUpdateException extends Exception
{
    public function __construct($message = "Payment settings update failed", $code = 500)
    {
        parent::__construct($message, $code);
    }
}
