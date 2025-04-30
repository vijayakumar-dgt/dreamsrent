<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'service_ratings',
        'location_ratings',
        'facility_ratings',
        'value_for_money_ratings',
        'cleanliness_ratings',
        'average_ratings',
    ];
}
