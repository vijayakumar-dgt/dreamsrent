<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetail extends Model
{
    use HasFactory, SoftDeletes;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

}
