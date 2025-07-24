<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class State
 *
 * @property int $id
 * @property string $name
 * @property int $country_id
 * @property int $status
 * @property-read Country $country
 */
class State extends Model
{
    /** @var string */
    protected $table = 'states';

    /** @var array<int, string> */
    protected $fillable = ['name', 'country_id', 'status'];

    // If your table does not have timestamps, uncomment the next line:
    // public $timestamps = false;

    /**
     * Get the country that owns the state.
     *
     * @return BelongsTo<Country, State>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Scope a query to only include active states.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
