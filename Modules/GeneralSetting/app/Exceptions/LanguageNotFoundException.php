<?php
namespace Modules\GeneralSetting\Exceptions;

use Exception;

class LanguageNotFoundException extends Exception
{
    public function __construct($message = null)
    {
        $message = $message ?? __('admin.general_settings.language_not_found');
        parent::__construct($message);
    }
}
