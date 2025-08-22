<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;

class AddReviewRequest extends CustomFailedValidation
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comments' => 'required|min:3',
        ];
    }

    public function messages(): array
    {
        return [
            'comments.required' => __('web.home.comments_required'),
            'comments.min'      => __('web.home.comments_minlength'),
        ];
    }
}
