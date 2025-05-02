<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\VehicleMetaFactory;

class VehicleMeta extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'vehicle_metas';

    protected $fillable = [
        'vehicle_id',
        'key',
        'value',
    ];

    // protected static function newFactory(): VehicleMetaFactory
    // {
    //     // return VehicleMetaFactory::new();
    // }
}
