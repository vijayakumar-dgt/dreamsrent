<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class StoreAwsSettingsRequest extends CustomFailedValidation
{
    /**
     * Common validation rule for required string fields.
     */
    private const REQUIRED_STRING = 'required|string';

    public function rules(): array
    {
        return [
            'aws_access_key'  => self::REQUIRED_STRING,
            'aws_secret_key'  => self::REQUIRED_STRING,
            'aws_region'      => self::REQUIRED_STRING,
            'aws_bucket_name' => self::REQUIRED_STRING,
            'aws_base_url'    => 'required|url',
        ];
    }

    public function messages(): array
    {
        return [
            'aws_access_key.required'  => __('The AWS access key field is required.'),
            'aws_secret_key.required'  => __('The AWS secret access key field is required.'),
            'aws_region.required'      => __('The AWS region field is required.'),
            'aws_bucket_name.required' => __('The AWS bucket field is required.'),
            'aws_base_url.required'    => __('The AWS URL field is required.'),
            'aws_base_url.url'         => __('The AWS URL must be a valid URL.'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
