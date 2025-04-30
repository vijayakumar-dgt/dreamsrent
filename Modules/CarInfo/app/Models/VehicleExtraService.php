<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\CarInfo\Database\Factories\VehicleExtraServiceFactory;

class VehicleExtraService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $fillable = ['vehicle_id', 'extra_service_id', 'value', 'price'];

    // protected static function newFactory(): VehicleExtraServiceFactory
    // {
    //     // return VehicleExtraServiceFactory::new();
    // }

    public function extraService()
    {
        return $this->belongsTo(ExtraService::class, 'extra_service_id', 'id');
    }
}
