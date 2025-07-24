<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * VehicleDamage Model
 *
 * @property int $id
 * @property int $vehicle_id
 * @property string $damage_type
 * @property string $damage_loaction
 * @property string $image
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class VehicleDamage extends Model
{
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
}
