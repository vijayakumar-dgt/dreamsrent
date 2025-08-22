<?php

namespace Modules\Communication\Exceptions;

/**
 * Exception thrown when required SMTP settings are missing or incomplete.
 */
class SmtpConfigurationException extends \LogicException
{
    public function __construct($message = "SMTP settings are incomplete. Please check your configuration.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}