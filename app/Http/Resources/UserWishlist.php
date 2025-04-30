<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWishlist extends JsonResource
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
            'vehicle_id' => $this->vehicle_id,
            'name' => $this->vehicle->name ?? "",
            'slug' => $this->vehicle->slug ?? "",
            'vehicle_image' => uploadedAsset($this->vehicle->vehicle_image),
            'brand' => $this->vehicle->brand->brand_name ?? null,
            'car_type' => $this->vehicle->carType->name ?? null,
            'category' => $this->vehicle->category->name ?? null,
            'location' => $this->vehicle->mainLocation->name ?? null,
            'color' => $this->vehicle->color->name ?? null,
            'fuel_type' => $this->vehicle->fuel_type->fuel_type ?? null,
            'transmission' => $this->vehicle->transmission->name ?? null,
            'year' => $this->vehicle->year,
            'mileage' => $this->vehicle->mileage,
            'passenger_capacity' => $this->vehicle->passenger_capacity,
            'num_seats' => $this->vehicle->num_seats,
            'num_doors' => $this->vehicle->num_doors,
            'num_airbags' => $this->vehicle->num_airbags,
            'vehicle_video' => $this->vehicle->vehicle_video,
            'features' => $this->vehicle->features,
            'currency' => getDefaultCurrencySymbol(),
            'price'    => $this->getPrice($this->vehicle),
            'filtered_price' => $this->getPrice($this->vehicle, true, true),
            'rating'   => rand(1, 5),
        ];
    }

    public function getPrice($vehicle, $firstPrice = false, $type = false)
    {
        $vehiclePrices = json_decode($vehicle->vehicle_price, true);
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
