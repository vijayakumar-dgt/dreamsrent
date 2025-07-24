<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DrivingType Model
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Booking\Models\Booking[] $bookings
 */
class DrivingType extends Model
{
    use HasFactory;

    protected $table = 'driving_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    // Add casts if you have more fields in the future
    // protected $casts = [
    //     'status' => 'integer',
    // ];

    // Uncomment if your table does NOT have created_at/updated_at columns
    // public $timestamps = false;

    /**
     * Get the bookings for the driving type.
     *
     * @return HasMany<\Modules\Booking\Models\Booking>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(\Modules\Booking\Models\Booking::class, 'driving_type');
    }
}
