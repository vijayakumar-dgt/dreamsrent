<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class SignatureSettingRequest extends CustomFailedValidation
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'signature_name' => 'required|string|max:255',
            'is_default' => 'nullable',
            'status' => 'nullable|boolean',
        ];

        if (empty($this->id)) {
            $rules['id'] = 'required|integer|exists:signature_settings,id';
            $rules['signature_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';
        } else {
            $rules['signature_image'] = 'image|mimes:jpeg,png,jpg,gif|max:5120';
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'signature_name.required' => __('The signature name is required.'),
            'signature_name.max' => __('The signature name may not be greater than 255 characters.'),
            'signature_image.required' => __('The signature image is required.'),
            'signature_image.image' => __('The file must be an image.'),
            'signature_image.mimes' => __('The image must be a file of type: jpeg, png, jpg, gif.'),
            'signature_image.max' => __('The image may not be greater than 5MB.'),
            'id.required' => __('The signature ID is required.'),
            'id.exists' => __('The selected signature is invalid.'),
        ];
    }
}
