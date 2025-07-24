<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;

class AddReplyRequest extends CustomFailedValidation
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
            'reply_comments' => 'required|min:3',
        ];
    }

    public function messages(): array
    {
        return [
            'reply_comments.required' => __('web.home.reply_comments_required'),
            'reply_comments.min'      => __('web.home.reply_comments_minlength'),
        ];
    }
}
