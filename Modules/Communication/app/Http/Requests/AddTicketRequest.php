<?php

namespace Modules\Communication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required',
            'priority' => 'required|string|in:Low,Medium,High',
            'description' => 'required|string|max:1000',
            'document' => 'array|max:10',
            'document.*' => 'nullable|file|mimes:pdf,txt,doc,docx|max:10240',
        ];
    }
}
