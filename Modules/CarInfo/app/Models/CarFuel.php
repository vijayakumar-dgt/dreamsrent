<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\CarFuelFactory;

class CarFuel extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ["fuel_type", "language_id", "status"];

    // protected static function newFactory(): CarFuelFactory
    // {
    //     // return CarFuelFactory::new();
    // }
}
