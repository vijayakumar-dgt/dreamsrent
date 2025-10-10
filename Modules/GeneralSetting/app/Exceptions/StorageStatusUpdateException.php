<?php
namespace Modules\GeneralSetting\Exceptions;

use Exception;

class StorageStatusUpdateException extends Exception
{
    public function __construct($message = "Storage status update failed", $code = 500)
    {
        parent::__construct($message, $code);
    }
}
