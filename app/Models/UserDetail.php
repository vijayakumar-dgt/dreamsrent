<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'parent_id',
        'profile_image',
        'first_name',
        'last_name',
        'mobile_number',
        'gender',
        'dob',
        'address',
        'card_number',
        'date_of_issue',
        'valid_date',
        'country_id',
        'state_id',
        'city_id',
        'postal_code',
        'latitude',
        'longitude',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userDetail(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }
}
