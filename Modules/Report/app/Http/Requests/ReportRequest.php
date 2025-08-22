<?php

namespace Modules\Report\Http\Requests;

use App\Library\CustomFailedValidation;

class ReportRequest extends CustomFailedValidation
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
        // Add validation rules as needed for report-specific requests
        return [
            // Example: 'date_range' => 'nullable|string',
            // Example: 'vehicle_id' => 'nullable|integer|exists:vehicle_info,id',
        ];
    }
}
