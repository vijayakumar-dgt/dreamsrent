<?php

namespace Modules\CarInfo\Http\Requests;

use App\Library\CustomFailedValidation;
use Illuminate\Validation\Validator;
use Carbon\Carbon;
use Modules\CarInfo\Models\Maintenance;

class MaintenanceRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $start = $this->start_date
            ? Carbon::createFromFormat('d-m-Y', $this->start_date)->format('Y-m-d')
            : null;
        $end = $this->end_date
            ? Carbon::createFromFormat('d-m-Y', $this->end_date)->format('Y-m-d')
            : null;

        $this->merge([
            'start_date' => $start,
            'end_date'   => $end,
        ]);
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required'],
            'odometer' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'details' => [
                'required',
                'not_regex:/<\/?script\b[^>]*>/i'
            ],
            'status' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => __('admin.rentals.vehicle_required'),
            'odometer.required' => __('admin.rentals.odometer_required'),
            'start_date.required' => __('admin.rentals.start_date_required'),
            'end_date.required' => __('admin.rentals.end_date_required'),
            'details.required' => __('admin.rentals.details_required'),
            'details.not_regex' => __('admin.common.script_tag_not_allowed'),
            'status.required' => __('admin.rentals.status_required'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $vehicleId = $this->vehicle_id;
            $startDate = $this->start_date;
            $endDate = $this->end_date;
            $id = $this->id ?? null;

            $query = Maintenance::where('vehicle_id', $vehicleId)
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
                });

            if ($id) {
                $query->where('id', '!=', $id);
            }

            if ($query->exists()) {
                $validator->errors()->add('start_date', __('admin.rentals.date_overlap'));
                $validator->errors()->add('end_date', __('admin.rentals.date_overlap'));
            }
        });
    }
}
