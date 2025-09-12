<?php

namespace Modules\GeneralSetting\Exceptions;

use Exception;

class TaxRateSaveException extends Exception
{
    public function __construct(string $message = 'Failed to save tax rate', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
