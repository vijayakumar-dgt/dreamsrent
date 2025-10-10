<?php
namespace Modules\GeneralSetting\Exceptions;

use Exception;

class CurrencyNotFoundException extends Exception
{
    public function __construct($message = "Currency not found", $code = 404)
    {
        parent::__construct($message, $code);
    }
}
