<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class TestimonialAddRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'     => 'required|string|max:255',
            'customer_rating'   => 'required|integer|min:1|max:5',
            'customer_review'   => 'required|string',
            'testimonial_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'testimonial_image.required' => 'The testimonial image is required for creating a new testimonial.',
        ];
    }
}
