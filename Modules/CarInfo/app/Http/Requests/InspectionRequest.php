<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;

class InspectionRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_info_id'    => 'required|exists:vehicle_info,id',
            'inspection_date'    => 'required|date|after_or_equal:today',
            'inspection_by'      => 'required|exists:users,id',
            'odometer'           => 'required|numeric|min:0',
            'fuel'               => 'required|numeric|min:0',
            'inspection_status'  => 'required',
            'repair_status'      => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_info_id.required'       => __('admin.rentals.vehicle_required'),
            'vehicle_info_id.exists'         => __('admin.rentals.vehicle_invalid'),
            'inspection_date.required'       => __('admin.rentals.inspection_date_required'),
            'inspection_date.date'           => 'Please enter valid date',
            'inspection_date.after_or_equal' => 'Please enter future date',
            'inspection_by.required'         => __('admin.rentals.inspection_by_required'),
            'inspection_by.exists'           => 'Please select valid user',
            'odometer.required'              => __('admin.rentals.odometer_required'),
            'odometer.numeric'               => __('admin.rentals.odometer_valid'),
            'odometer.min'                   => __('admin.rentals.odometer_valid'),
            'fuel.required'                  => __('admin.rentals.fuel_required'),
            'fuel.numeric'                   => __('admin.rentals.fuel_valid'),
            'fuel.min'                       => __('admin.rentals.fuel_valid'),
            'inspection_status.required'     => __('admin.rentals.inspection_status_required'),
            'repair_status.required'         => __('admin.rentals.repair_status_required'),
        ];
    }
}
