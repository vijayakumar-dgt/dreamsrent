<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\SeatTypeFactory;

class SeatType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = "seat_types";

    protected $fillable = ["seat_type", "status"];

    // protected static function newFactory(): SeatTypeFactory
    // {
    //     // return SeatTypeFactory::new();
    // }
}
