<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\CarInfo\Database\Factories\VehicleDamageFactory;

class VehicleDamage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'vehicle_damages';
    protected $fillable = [
        'vehicle_id',
        'damage_type',
        'damage_loaction',
        'image',
        'description',
    ];

    // protected static function newFactory(): VehicleDamageFactory
    // {
    //     // return VehicleDamageFactory::new();
    // }
}
