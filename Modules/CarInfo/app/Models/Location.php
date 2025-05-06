<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property int|null $language_id
 * @property int $status
 * @property string|array<string>|null $image
 * @property int $country
 * @property int $state
 * @property int $city
 * @property string $pincode
 * @property string|array<string>|null $image_url
 * @property string|null $working_days
 * @property int|null $main_location_id
 */
class Location extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['id', 'name'];

    protected static function booted()
    {
        static::deleting(function ($location) {
            $location->workingDays()->delete();
        });
    }
    /**
     * @return HasMany<LocationWorkingDay,Location>
     */
    public function workingDays(): HasMany
    {
        /** @var HasMany<LocationWorkingDay, Location>*/
        return $this->hasMany(LocationWorkingDay::class);
    }
}
