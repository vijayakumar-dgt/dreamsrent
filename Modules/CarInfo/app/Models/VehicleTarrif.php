<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\CarInfo\Database\Factories\VehicleTarrifFactory;

class VehicleTarrif extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = "vehicle_tarrifs";

    protected $fillable = ["vehicle_id", "tariff_title", "tariff_daily_price", "tariff_from_days", "tariff_to_days", "tariff_base_km", "tariff_extra_price"];

    // protected static function newFactory(): VehicleTarrifFactory
    // {
    //     // return VehicleTarrifFactory::new();
    // }
}
