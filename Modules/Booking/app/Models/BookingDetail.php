<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'has_tariff',
        'vehicle_tariff_id',
        'has_season',
        'vehicle_season_id',
        'vehicle_price_type',
        'tariff_title',
        'tariff_price',
        'tariff_from_days',
        'tariff_to_days',
        'tariff_base_km',
        'tariff_extra_price',
        'seasonal_title',
        'seasonal_start_date',
        'seasonal_end_date',
        'seasonal_daily_rate',
        'seasonal_monthly_rate',
        'seasonal_weekly_rate',
        'seasonal_late_fee',
    ];
}
