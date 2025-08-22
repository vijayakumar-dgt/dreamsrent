<?php

namespace Modules\Communication\Exceptions;

use Exception;

class InvalidStatusTransitionException extends Exception
{
    public function __construct(string $message = 'Invalid status transition', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
