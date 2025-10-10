<?php

namespace App\Http\Resources;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CarInfo\Models\VehicleInfo;

/** @mixin \App\Models\Wishlist */
/**
 * @property \Modules\Booking\Models\Booking $resource
 */
class UserWishlist extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $vehicle = $this->resource->vehicle;

        return [
            'id'                 => $this->resource->id,
            'vehicle_id'         => $this->resource->getAttribute('vehicle_id'),
            'name'               => $vehicle->name ?? '',
            'slug'               => $vehicle->slug ?? '',
            'vehicle_image'      => uploadedAsset($vehicle->vehicle_image ?? ''),
            'brand'              => $vehicle->brand->brand_name ?? null,
            'car_type'           => $vehicle->carType->name ?? null,
            'category'           => $vehicle->category->name ?? null,
            'location'           => $vehicle->mainLocation->name ?? null,
            'color'              => $vehicle->color->name ?? null,
            'fuel_type'          => $vehicle->fuel_type->fuel_type ?? null,
            'transmission'       => $vehicle->transmission->name ?? null,
            'year'               => $vehicle->year ?? null,
            'mileage'            => $vehicle->mileage ?? null,
            'passenger_capacity' => $vehicle->passenger_capacity ?? null,
            'num_seats'          => $vehicle->num_seats ?? null,
            'num_doors'          => $vehicle->num_doors ?? null,
            'num_airbags'        => $vehicle->num_airbags ?? null,
            'vehicle_video'      => $vehicle->vehicle_video ?? null,
            'features'           => $vehicle->features ?? [],
            'currency'           => getDefaultCurrencySymbol(),
            'price'              => ($vehicle instanceof \Modules\CarInfo\Models\VehicleInfo)
                ? $this->getPrice($vehicle)
                : 0,
            'filtered_price' => ($vehicle instanceof \Modules\CarInfo\Models\VehicleInfo)
                ? $this->getPrice($vehicle, true, true)
                : 0,
            'rating' => $this->getRating($vehicle->id),
        ];
    }

    /**
     * Get the vehicle price(s) either as full list or just the first one.
     *
     * @return array<string, mixed>
     */
    public function getPrice(VehicleInfo $vehicle, bool $firstPrice = false, bool $type = false): array
    {
        $vehiclePrices = json_decode($vehicle->vehicle_price ?? '[]', true);
        $filteredPrices = [];

        if (!empty($vehiclePrices) && is_array($vehiclePrices)) {
            foreach ($vehiclePrices as $price) {
                foreach ($price as $key => $value) {
                    if ($value > 0) {
                        $filteredPrices[$key] = $value; // Store as associative array
                    }
                }
            }
        }

        if ($firstPrice) {
            // Get the first key-value pair correctly
            $firstKey = array_key_first($filteredPrices);
            return [
                'type'  => $firstKey,
                'value' => $filteredPrices[$firstKey]
            ];
        }

        return $filteredPrices;
    }

    public function getRating(?int $vehicleId): float
    {
        $rating = Review::where('vehicle_id', $vehicleId)->avg('average_ratings');
        return $rating ?? 0;
    }
}
