<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CarInfo\Models\VehicleInfo;

/**
 * Review Model
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $user_id
 * @property int $service_ratings
 * @property int $location_ratings
 * @property int $facility_ratings
 * @property int $value_for_money_ratings
 * @property int $cleanliness_ratings
 * @property float $average_ratings
 * @property-read \App\Models\User $user
 * @property-read VehicleInfo $vehicle
 * @property-read \Illuminate\Database\Eloquent\Collection|ReviewMessages[] $messages
 */
class Review extends Model
{
    use SoftDeletes;

    // If your table name is not 'reviews', uncomment and set it
    // protected $table = 'reviews';

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

    /**
     * Get the user who wrote the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the vehicle for this review.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleInfo::class, 'vehicle_id');
    }

    /**
     * Get all messages (comments/replies) for this review.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ReviewMessages::class, 'review_id');
    }
}
