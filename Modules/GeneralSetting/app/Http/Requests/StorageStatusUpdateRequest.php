<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorageStatusUpdateRequest extends FormRequest
{
   public function rules(): array
    {
        return [
            'storage_type' => 'required|in:local_storage,aws_storage',
            'status' => 'required|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
