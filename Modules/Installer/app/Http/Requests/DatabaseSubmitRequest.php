<?php

namespace Modules\Installer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Installer\Enums\InstallerInfo;

class DatabaseSubmitRequest extends FormRequest
{
    private const REQUIRED_STRING = 'required|string';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host'           => 'required|ip',
            'port'           => 'required|numeric',
            'database'       => self::REQUIRED_STRING,
            'user'           => self::REQUIRED_STRING,
            'db_pass'        => InstallerInfo::isRemoteLocal() ? 'nullable' : self::REQUIRED_STRING,
            'reset_database' => 'nullable|string',
            'fresh_install'  => 'nullable|boolean',
        ];
    }
}
