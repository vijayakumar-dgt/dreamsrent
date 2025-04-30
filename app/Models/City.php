<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = ['name', 'state_id', 'status'];

    
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function country(): HasOneThrough
    {
        return $this->hasOneThrough(Country::class, State::class, 'id', 'id', 'state_id', 'country_id');
    }
}
