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
     * @return BelongsTo<State, City>
     */
    public function state(): BelongsTo
    {
        /** @var BelongsTo<State, City> */
        return $this->belongsTo(State::class);
    }

    /**
     * @return HasOneThrough<Country, State, City>
     */
    public function country(): HasOneThrough
    {
        /** @var HasOneThrough<Country, State, City> */
        return $this->hasOneThrough(
            Country::class,
            State::class,
            'id',
            'id',
            'state_id',
            'country_id'
        );
    }
}
