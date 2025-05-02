<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $reply_date
 * @property string|array|null $profile_image
 * @property string|null $full_name
 * @property string|array<string>|null $vehicle_image
 * @property string|null $review_date
 * @property string|null $user_name
 * @property string|array|null $replies
 */
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
