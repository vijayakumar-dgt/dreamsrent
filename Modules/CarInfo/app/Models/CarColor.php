<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\CarColorFactory;

class CarColor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = "car_colors";

    protected $fillable = ["name", "language_id", "value", "status"];

    // protected static function newFactory(): CarColorFactory
    // {
    //     // return CarColorFactory::new();
    // }
}
