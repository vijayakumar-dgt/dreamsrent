<?php

namespace Modules\Communication\Exceptions;

/**
 * Exception thrown when required SendGrid settings are missing or incomplete.
 */
class SendGridConfigurationException extends \LogicException
{
    public function __construct($message = "SendGrid settings are incomplete. Please check your configuration.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
