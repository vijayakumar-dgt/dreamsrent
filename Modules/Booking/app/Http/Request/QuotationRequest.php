<?php

namespace Modules\Booking\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
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
            'start_date' => 'required',
            'start_time' => 'required',
            'end_date' => 'required',
            'end_time' => 'required',
            'pickup_location' => 'required',
            'return_location' => 'required',
            'vehicle_id' => 'required',
            'customer_id' => 'required',
            'vehicle_price' => 'required',
            'extra_service' => 'required',
            'insurance' => 'required',
            'tax_val' => 'required',
            'tax_type' => 'required',
        ];
    }
}
