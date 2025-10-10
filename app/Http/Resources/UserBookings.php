<?php

namespace App\Http\Resources;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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

        $vehicle = $resource->vehicle;
        $vehicleImage = $this->getVehicleImage($vehicle);

        return [
            'id'                     => $resource->id,
            'vehicle_id'             => $resource->vehicle_id,
            'reservation_id'         => $resource->reservation_id,
            'vehicle_name'           => $vehicle->name ?? '',
            'vehicle_image'          => uploadedAsset($vehicleImage),
            'vehicle_page_url'       => $vehicle ? route('vehicleDetails', $vehicle->slug) : '',
            'driving_type'           => $resource->delivery_type ? ucfirst($resource->delivery_type) : '',
            'rental_type'            => $resource->rental_type ?? '',
            'main_location'          => $vehicle->mainLocation->name ?? '',
            'pickup_location'        => $resource->pickupLocation->name ?? '',
            'return_location'        => $resource->returnLocation->name ?? '',
            'start_datetime'         => $resource->start_datetime,
            'end_datetime'           => $resource->end_datetime,
            'formated_start_datetime'=> formatDateTime($resource->start_datetime),
            'formated_end_datetime'  => formatDateTime($resource->end_datetime),
            'booked_on'              => $resource->created_at,
            'formated_booked_on'     => formatDateTime($resource->created_at),
            'total_amount'           => $resource->final_price,
            'currency'               => getDefaultCurrencySymbol(),
            'status'                 => $resource->booking_status,
            'payment_status'         => $resource->payment_status,
            'payment_type'           => $resource->payment_type,
            'extra_services'         => $this->getExtraServices($resource->extra_service),
            'cancel_reason'          => $resource->cancel_reason,
            'cancel_date'            => $resource->cancel_date,
            'formated_cancel_date'   => formatDateTime($resource->cancel_date),
            'cancel_by'              => $resource->cancelledUser->name ?? '',
            'no_of_passengers'       => $resource->no_of_passengers,
            'customer'               => $resource->customer ?? null,
            'customer_detail'        => $resource->customerDetail ?? null,
            'booking_user_info'      => $resource->userInfo ?? null,
            'review_added'           => $this->reviewAdded($resource->vehicle_id),
        ];
    }

    /**
     * Get the appropriate vehicle image, using thumbnail if it exists.
     */
    private function getVehicleImage($vehicle): string
    {
        if (!$vehicle || !$vehicle->vehicle_image) {
            return '';
        }

        $filename = basename($vehicle->vehicle_image);
        $thumbnailPath = 'vehicles/images/thumbnail/' . $filename;
        $fullPath = public_path('storage/' . $thumbnailPath);

        return file_exists($fullPath) ? $thumbnailPath : $vehicle->vehicle_image ?? '';
    }

    /**
     * @param array<int, object{id: int}>|string|null $extraserviceData
     */
    public function getExtraServices(array|string|null $extraserviceData): string
    {
        $extraServices = [];

        if (is_array($extraserviceData)) {
            /** @var array<int, object{id: int}> $extraservice */
            $extraservice = $extraserviceData;
        } elseif (is_string($extraserviceData)) {
            /** @var array<int, object{id: int}> $extraservice */
            $extraservice = json_decode($extraserviceData);
        } else {
            $extraservice = [];
        }

        if (!empty($extraservice)) {
            /** @var \Illuminate\Support\Collection<int, object{id: int}> $collection */
            $collection = collect($extraservice);
            $ids = $collection->pluck('id')->toArray();

            $extraServices = ExtraService::whereIn('id', $ids)->pluck('name')->toArray();
            return implode(", ", $extraServices);
        }

        return "";
    }

    public function reviewAdded($vehicle_id)
    {
        $authUser = currentUser();
        $review = Review::where('vehicle_id', $vehicle_id)->where('user_id', $authUser->id)->first();
        return (bool) $review;
    }
}
