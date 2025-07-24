<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $driver_name
 * @property string|array<string>|null $image
 * @property string $assigned_cars
 * @property array<string,mixed> $vehicle
 * @property string $valid_date
 */
class Driver extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'driver_name',
        'image',
        'gender',
        'phone_number',
        'address',
        'card_number',
        'email',
        'assigned_cars',
        'date_of_issue',
        'valid_date',
    ];

    /**
     * @return HasMany<DriverDocument, Driver>
     */
    public function documents(): HasMany
    {
        /**
         * @var HasMany<DriverDocument, Driver>
         */
        return $this->hasMany(DriverDocument::class, 'driver_id');
    }
}
