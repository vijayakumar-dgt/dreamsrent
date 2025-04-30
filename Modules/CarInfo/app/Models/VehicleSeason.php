<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\CarInfo\Database\Factories\VehicleSeasonFactory;

class VehicleSeason extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = "vehicle_seasons";

    protected $fillable = [ "vehicle_id", "seasonal_title", "seasonal_start_date", "seasonal_end_date", "seasonal_daily_rate", "seasonal_weekly_rate", "seasonal_monthly_rate", "seasonal_late_fee" ];

    // protected static function newFactory(): VehicleSeasonFactory
    // {
    //     // return VehicleSeasonFactory::new();
    // }
}
