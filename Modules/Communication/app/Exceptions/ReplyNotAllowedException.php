<?php

namespace Modules\Communication\Exceptions;

use Exception;

class ReplyNotAllowedException extends Exception
{
    public function __construct(string $message = 'Reply is allowed only when ticket is in status 3', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
