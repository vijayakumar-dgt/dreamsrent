<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Model;

class BookingHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'data',
        'action',
        'message',
    ];
}
