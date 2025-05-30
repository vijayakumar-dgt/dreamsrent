<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Rule;

class VehicleTransmissionRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?? null;
        $authUser = current_user();
        $languageId = $authUser->language_id ?? 1;

        return [
            'name' => [
                'required',
                'max:30',
                'not_regex:/<\/?script\b[^>]*>/i',
                Rule::unique('transmissions', 'name')
                    ->ignore($id)
                    ->whereNull('deleted_at')
                    ->where('language_id', $languageId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => __('admin.rentals.vehicle_transmission_required'),
            'name.unique'      => __('admin.rentals.vehicle_transmission_unique'),
            'name.max'         => __('admin.rentals.vehicle_transmission_maxlenght'),
            'name.not_regex'   => __('admin.common.script_tag_not_allowed'),
        ];
    }
}
