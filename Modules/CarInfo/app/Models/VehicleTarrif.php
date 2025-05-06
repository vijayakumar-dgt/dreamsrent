<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $tariff_title
 * @property float|null $tariff_daily_price
 * @property int|null $tariff_from_days
 * @property int|null $tariff_to_days
 * @property float|null $tariff_base_km
 * @property float|null $tariff_extra_price
 * @property string $deleted_at
 */
class VehicleTarrif extends Model
{
    /**
     * The attributes that are mass assignable.
     */

    protected $table = "vehicle_tarrifs";

    protected $fillable = ["vehicle_id", "tariff_title", "tariff_daily_price", "tariff_from_days", "tariff_to_days", "tariff_base_km", "tariff_extra_price"];
}
