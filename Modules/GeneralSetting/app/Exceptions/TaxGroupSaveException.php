<?php

namespace Modules\GeneralSetting\Exceptions;

use Exception;

class TaxGroupSaveException extends Exception
{
    public function __construct(string $message = 'Failed to save tax group', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
