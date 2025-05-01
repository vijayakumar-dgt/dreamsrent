<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Country;

class State extends Model
{
    protected $table = 'states';

    protected $fillable = ['name', 'country_id', 'status'];

    /**
     * @return BelongsTo<Country, State>
     */
    public function country(): BelongsTo
    {
        /** @var BelongsTo<Country, State> */
        return $this->belongsTo(Country::class, 'country_id');
    }
}
