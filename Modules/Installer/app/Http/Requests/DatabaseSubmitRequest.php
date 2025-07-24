<?php

namespace Modules\Installer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Installer\Enums\InstallerInfo;

class DatabaseSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host'           => 'required|ip',
            'port'           => 'required|numeric',
            'database'       => 'required|string',
            'user'           => 'required|string',
            'db_pass'        => InstallerInfo::isRemoteLocal() ? 'nullable' : 'required|string',
            'reset_database' => 'nullable|string',
            'fresh_install'  => 'nullable|boolean',
        ];
    }
}
