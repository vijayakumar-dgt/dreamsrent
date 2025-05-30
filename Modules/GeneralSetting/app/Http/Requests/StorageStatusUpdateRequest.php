<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class StorageStatusUpdateRequest extends CustomFailedValidation
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
