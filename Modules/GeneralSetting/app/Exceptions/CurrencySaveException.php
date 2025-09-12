<?php

namespace Modules\GeneralSetting\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a currency cannot be saved.
 */
class CurrencySaveException extends Exception
{
    public function __construct(string $message = 'Failed to save currency', int $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
