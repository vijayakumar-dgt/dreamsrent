<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\CarSteeringFactory;

class CarSteering extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ["steering_type", "status"];

    // protected static function newFactory(): CarSteeringFactory
    // {
    //     // return CarSteeringFactory::new();
    // }
}
