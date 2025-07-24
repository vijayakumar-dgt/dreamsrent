<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property State $state
 * @property Country $country
 */
class City extends Model
{
    protected $table = 'cities';

    protected $fillable = ['name', 'state_id', 'status'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer', // or 'boolean' if status is boolean
    ];

    // Uncomment if you always want to eager load state and country
    // protected $with = ['state', 'country'];

    // If your table does NOT have created_at/updated_at, uncomment below:
    // public $timestamps = false;

    /**
     * Get the state that the city belongs to.
     *
     * @return BelongsTo<State, City>
     */
    public function state(): BelongsTo
    {
        /** @var BelongsTo<State, City> */
        return $this->belongsTo(State::class);
    }

    /**
     * Get the country through the state.
     *
     * @return HasOneThrough<Country, State, City>
     */
    public function country(): HasOneThrough
    {
        /** @var HasOneThrough<Country, State, City> */
        return $this->hasOneThrough(
            Country::class,
            State::class,
            'id',         // Foreign key on the states table...
            'id',         // Foreign key on the countries table...
            'state_id',   // Local key on the cities table...
            'country_id'  // Local key on the states table...
        );
    }
}
