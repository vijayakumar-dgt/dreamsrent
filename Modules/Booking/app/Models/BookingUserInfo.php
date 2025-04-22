<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Booking\Database\Factories\BookingUserInfoFactory;

class BookingUserInfo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'driver_first_name',
        'driver_last_name',
        'driver_age',
        'driver_mobile_number',
        'driver_licence',
        'driver_check',
        'first_name',
        'last_name',
        'no_person',
        'company',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'email',
        'phone_number',
        'add_info',
        'terms_check',
    ];

    // protected static function newFactory(): BookingUserInfoFactory
    // {
    //     // return BookingUserInfoFactory::new();
    // }
}
