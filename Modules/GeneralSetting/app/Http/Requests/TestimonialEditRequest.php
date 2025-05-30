<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialEditRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:testimonials,id',
            'customer_name' => 'required|string|min:3',
            'customer_rating' => 'required|integer|min:1|max:5',
            'customer_review' => 'required|string|min:10',
            'status' => 'required|boolean',
            'testimonial_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];
    }
}
