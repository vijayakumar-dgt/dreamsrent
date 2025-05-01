<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CarInfo\Models\VehicleInfo;

/** @mixin \App\Models\Wishlist */
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
            'id' => $this->resource->id,
            'vehicle_id' => $this->resource->vehicle_id,
            'name' => $vehicle->name ?? '',
            'slug' => $vehicle->slug ?? '',
            'vehicle_image' => isset($vehicle->vehicle_image) ? uploadedAsset($vehicle->vehicle_image) : null,
            'brand' => $vehicle->brand->brand_name ?? null,
            'car_type' => $vehicle->carType->name ?? null,
            'category' => $vehicle->category->name ?? null,
            'location' => $vehicle->mainLocation->name ?? null,
            'color' => $vehicle->color->name ?? null,
            'fuel_type' => $vehicle->fuel_type->fuel_type ?? null,
            'transmission' => $vehicle->transmission->name ?? null,
            'year' => $vehicle->year ?? null,
            'mileage' => $vehicle->mileage ?? null,
            'passenger_capacity' => $vehicle->passenger_capacity ?? null,
            'num_seats' => $vehicle->num_seats ?? null,
            'num_doors' => $vehicle->num_doors ?? null,
            'num_airbags' => $vehicle->num_airbags ?? null,
            'vehicle_video' => $vehicle->vehicle_video ?? null,
            'features' => $vehicle->features ?? [],
            'currency' => getDefaultCurrencySymbol(),
            'price' => $vehicle ? $this->getPrice($vehicle) : 0,
            'filtered_price' => $vehicle ? $this->getPrice($vehicle, true, true) : 0,
            'rating' => rand(1, 5),
        ];
    }

    /**
     * Get the vehicle price(s) either as full list or just the first one.
     *
     * @param VehicleInfo $vehicle
     * @param bool $firstPrice
     * @param bool $type
     * @return array<string, int|float>|array{type: string|null, value: int|float|null}
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
                'type' => $firstKey,
                'value' => $filteredPrices[$firstKey]
            ];
        }

        return $filteredPrices;
    }
}
