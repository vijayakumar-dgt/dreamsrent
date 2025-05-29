<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeoSetupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metaImage'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'metaTitle'         => 'required|string|min:5|max:255',
            'siteDescription'   => 'required|string|min:10|max:5000',
            'ogmetaTitle'       => 'required|string|min:5|max:255',
            'ogsiteDescription' => 'required|string|min:10|max:5000',
            'keywords'          => 'required|string|max:1000'
        ];
    }
}
