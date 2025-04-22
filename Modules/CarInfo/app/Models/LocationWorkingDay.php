<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CarInfo\Database\Factories\LocationWorkingDayFactory;

class LocationWorkingDay extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): LocationWorkingDayFactory
    // {
    //     // return LocationWorkingDayFactory::new();
    // }
}
