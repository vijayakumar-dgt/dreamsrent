<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CopyrightListRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id'    => 'required|integer',
            'language_id' => 'nullable|integer',
        ];
    }
}
