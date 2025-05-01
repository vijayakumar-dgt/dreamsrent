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
        $resource = $this->resource;
        return [
           'id' => $resource->id,
           'reservation_id' => $resource->reservation_id,
           'vehicle_name'   => $resource->vehicle ? $resource->vehicle->name : '',
           'vehicle_image'  => $resource->vehicle ? uploadedAsset($resource->vehicle->vehicle_image)
            : uploadedAsset('default.png'),
           'vehicle_page_url' => $resource->vehicle ? route('vehicleDetails', $resource->vehicle->slug) : '',
           'driving_type'   => $resource->delivery_type ? ucfirst($resource->delivery_type) : '',
           'rental_type'    => $resource->rental_type ? ($resource->rental_type) : '',
           'main_location'  => $resource->vehicle && $resource->vehicle->mainLocation ? $resource->vehicle->mainLocation->name : '',
           'pickup_location' => $resource->pickupLocation ? $resource->pickupLocation->name : '',
           'return_location' => $resource->returnLocation ? $resource->returnLocation->name : '',
           'start_datetime' => $resource->start_datetime,
           'end_datetime'   => $resource->end_datetime,
           'formated_start_datetime' => formatDateTime($resource->start_datetime),
           'formated_end_datetime'   => formatDateTime($resource->end_datetime),
           'booked_on'      => $resource->created_at,
           'formated_booked_on' => formatDateTime($resource->created_at),
           'total_amount'   => $resource->final_price,
           'currency'       => getDefaultCurrencySymbol(),
           'status'         => $resource->booking_status,
           'payment_status' => $resource->payment_status,
           'payment_type'   => $resource->payment_type,
           'extra_services' => $resource->getExtraServices($resource->extra_service),
           'cancel_reason'  => $resource->cancel_reason,
           'cancel_date'    => $resource->cancel_date,
           'formated_cancel_date' => formatDateTime($resource->cancel_date),
           'cancel_by'      => $resource->cancelledUser ? $resource->cancelledUser->name : '',
           'no_of_passengers' => $resource->no_of_passengers,
           'customer'       => $resource->customer ? $resource->customer  : null,
           'customer_detail' => $resource->customerDetail ? $resource->customerDetail : null
        ];
    }

    public function getExtraServices(?string $extraserviceJsonString): string
    {
        $extraServices = [];
        if (!empty($extraserviceJsonString)) {
            /** @var array<int, object{ id: int }> $extraservice */
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
