<?php
namespace Modules\GeneralSetting\Exceptions;

use Exception;

class PaymentStatusUpdateException extends Exception
{
    public function __construct($message = "Payment status update failed", $code = 500)
    {
        parent::__construct($message, $code);
    }
}
