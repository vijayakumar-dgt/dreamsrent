<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class EnquiryUpdateRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => 'required|string|max:500',
            'status'  => 'required|in:1,2,3',
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => __('admin.bookings.comment_required'),
            'status.required'  => __('admin.bookings.status_required'),
        ];
    }
}
