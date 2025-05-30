<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBackupRequest extends FormRequest
{
     public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:dbbackups,id'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
