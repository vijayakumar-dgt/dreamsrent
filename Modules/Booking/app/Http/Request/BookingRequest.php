<?php

namespace Modules\Booking\Http\Request;

use App\Library\CustomFailedValidation;

class BookingRequest extends CustomFailedValidation
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
            'start_date'      => 'required|date|after_or_equal:today',
            'start_time'      => 'required|date_format:H:i',
            'end_date'        => 'required|date|after:start_date',
            'end_time'        => 'required|date_format:H:i',
            'pickup_location' => 'required|integer|exists:locations,id',
            'return_location' => 'required|integer|exists:locations,id',
            'vehicle_id'      => 'required|integer|exists:vehicle_info,id',
            'customer_id'     => 'required|integer|exists:users,id',
            'vehicle_price'   => 'required|numeric|min:0',
            'extra_service'   => 'required|array',
            'extra_service.*' => 'integer|exists:extra_services,id',
            'insurance'       => 'required|array',
            'insurance.*'     => 'integer|exists:insurances,id',
            'rental_type'     => 'sometimes|in:daily,weekly,monthly,yearly',
            'delivery_type'   => 'sometimes|in:pickup,delivery',
            'driving_type'    => 'sometimes|integer|exists:driving_types,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Start date must be today or a future date.',
            'end_date.after' => 'End date must be after start date.',
            'pickup_location.exists' => 'Selected pickup location is invalid.',
            'return_location.exists' => 'Selected return location is invalid.',
            'vehicle_id.exists' => 'Selected vehicle is not available.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'vehicle_price.min' => 'Vehicle price must be greater than 0.',
            'extra_service.*.exists' => 'One or more selected extra services are invalid.',
            'insurance.*.exists' => 'One or more selected insurance options are invalid.',
        ];
    }
}
