<?php

namespace Modules\Communication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticketid'     => 'required|exists:tickets,id',
            'assign_staff' => 'required|exists:users,id',
            'reply'        => 'nullable|string|max:3000'
        ];
    }
}
