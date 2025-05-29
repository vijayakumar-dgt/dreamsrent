<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CopyrightUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => 'required|integer',
            'language' => 'required|integer',
            'copy_right_description' => 'required|string|min:10',
        ];
    }
}
