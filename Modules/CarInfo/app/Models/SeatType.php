<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeatType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = "seat_types";

    protected $fillable = ["seat_type", "status"];

}
