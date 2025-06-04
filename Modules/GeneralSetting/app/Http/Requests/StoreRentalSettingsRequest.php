<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class StoreRentalSettingsRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minAdvanceReservation' => 'nullable|integer|min:0',
            'maxAdvanceReservation' => 'nullable|integer|min:0',
            'cancellationBuffer' => 'nullable|integer|min:0',
            'rescheduleBuffer' => 'nullable|integer|min:0',
            'faq' => 'nullable|boolean',
            'damages' => 'nullable|boolean',
            'extraService' => 'nullable|boolean',
            'booking' => 'nullable|boolean',
            'enquiries' => 'nullable|boolean',
            'reservation' => 'nullable|boolean',
            'seasonalPricing' => 'nullable|boolean',
            'pricing' => 'nullable|string',
        ];
    }
}
