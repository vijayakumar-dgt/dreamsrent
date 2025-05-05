<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property string $seasonal_title
 * @property string $seasonal_start_date
 * @property string $seasonal_end_date
 * @property float $seasonal_daily_rate
 * @property float $seasonal_weekly_rate
 * @property float $seasonal_monthly_rate
 * @property float $seasonal_late_fee
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class VehicleSeason extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $table = "vehicle_seasons";

    protected $fillable = [ "vehicle_id", "seasonal_title", "seasonal_start_date", "seasonal_end_date", "seasonal_daily_rate", "seasonal_weekly_rate", "seasonal_monthly_rate", "seasonal_late_fee" ];
}
