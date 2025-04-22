<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\ExtraService;

class UserBookings extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
           'id' => $this->id,
           'reservation_id' => $this->reservation_id,
           'vehicle_name'   => $this->vehicle ? $this->vehicle->name : '',
           'vehicle_image'  =>  uploadedAsset($this->vehicle->vehicle_image),
           'vehicle_page_url' => $this->vehicle ? route('vehicleDetails', $this->vehicle->slug) : '',
           'driving_type'   => $this->delivery_type ? ucfirst($this->delivery_type) : '',
           'rental_type'    => $this->rental_type ? ($this->rental_type) : '',
           'main_location'  => $this->vehicle && $this->vehicle->mainLocation ? $this->vehicle->mainLocation->name : '',
           'pickup_location'=> $this->pickupLocation ? $this->pickupLocation->name : '',
           'return_location'=> $this->returnLocation ? $this->returnLocation->name : '',
           'start_datetime' => $this->start_datetime,
           'end_datetime'   => $this->end_datetime,
           'formated_start_datetime' => formatDateTime($this->start_datetime),
           'formated_end_datetime'   => formatDateTime($this->end_datetime),
           'booked_on'      => $this->created_at,
           'formated_booked_on' => formatDateTime($this->created_at),
           'total_amount'   => $this->final_price,
           'currency'       => getDefaultCurrencySymbol(),
           'status'         => $this->booking_status,
           'payment_status' => $this->payment_status,
           'payment_type'   => $this->payment_type,
           'extra_services' => $this->getExtraServices($this->extra_service),
           'cancel_reason'  => $this->cancel_reason,
           'cancel_date'    => $this->cancel_date,
           'formated_cancel_date' => formatDateTime($this->cancel_date),
           'cancel_by'      => $this->cancelledUser ? $this->cancelledUser->name : '',
           'no_of_passengers' => $this->no_of_passengers,
           'customer'       => $this->customer ? $this->customer  : null,
           'customer_detail' => $this->customerDetail ? $this->customerDetail : null
       ];
    }

    public function getExtraServices($extraserviceJsonString)
    {
        $extraServices = [];
        if (!empty($extraserviceJsonString)) {
            $extraservice = json_decode($extraserviceJsonString);
            if (!empty($extraservice)) {
                $extraServices = collect($extraservice)->pluck('id')->toArray();
                $extraServices = ExtraService::whereIn('id', $extraServices)->pluck('name')->toArray();
                return implode(", ", $extraServices);
            }
        }
        return "";
    }
}
