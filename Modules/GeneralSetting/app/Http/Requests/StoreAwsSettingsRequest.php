<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class StoreAwsSettingsRequest extends CustomFailedValidation
{
   public function rules(): array
    {
        return [
            'aws_access_key' => 'required|string',
            'aws_secret_key' => 'required|string',
            'aws_region' => 'required|string',
            'aws_bucket_name' => 'required|string',
            'aws_base_url' => 'required|url',
        ];
    }

    public function messages(): array
    {
        return [
            'aws_access_key.required' => __('The AWS access key field is required.'),
            'aws_secret_key.required' => __('The AWS secret access key field is required.'),
            'aws_region.required' => __('The AWS region field is required.'),
            'aws_bucket_name.required' => __('The AWS bucket field is required.'),
            'aws_base_url.required' => __('The AWS URL field is required.'),
            'aws_base_url.url' => __('The AWS URL must be a valid URL.'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
