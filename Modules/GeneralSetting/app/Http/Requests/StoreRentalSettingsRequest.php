<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class StoreRentalSettingsRequest extends CustomFailedValidation
{
    /**
     * Common validation rules.
     */
    private const NULLABLE_INTEGER_MIN_ZERO = 'nullable|integer|min:0';
    private const NULLABLE_BOOLEAN = 'nullable|boolean';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minAdvanceReservation' => self::NULLABLE_INTEGER_MIN_ZERO,
            'maxAdvanceReservation' => self::NULLABLE_INTEGER_MIN_ZERO,
            'cancellationBuffer'    => self::NULLABLE_INTEGER_MIN_ZERO,
            'rescheduleBuffer'      => self::NULLABLE_INTEGER_MIN_ZERO,
            'faq'                   => self::NULLABLE_BOOLEAN,
            'damages'               => self::NULLABLE_BOOLEAN,
            'extraService'          => self::NULLABLE_BOOLEAN,
            'booking'               => self::NULLABLE_BOOLEAN,
            'enquiries'             => self::NULLABLE_BOOLEAN,
            'reservation'           => self::NULLABLE_BOOLEAN,
            'seasonalPricing'       => self::NULLABLE_BOOLEAN,
            'pricing'               => 'nullable|string',
        ];
    }
}
