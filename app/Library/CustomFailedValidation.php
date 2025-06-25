<?php

namespace App\Library;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomFailedValidation extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
            'code'   => 422,
            'errors' => $validator->errors()->toArray(),
            ], 422)
        );
    }
}
