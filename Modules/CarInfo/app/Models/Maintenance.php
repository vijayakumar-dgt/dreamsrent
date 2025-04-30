<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\MaintananceFactory;

class Maintenance extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vehicle_id',
        'odometer',
        'start_date',
        'end_date',
        'details',
        'status'
    ];


    public static $planned = 1;
    public static $inprogress = 2;
    public static $completed = 3;

    public function car()
    {
        return $this->hasOne(VehicleInfo::class, 'id');
    }

    public function vehicle()
    {
        return $this->belongsTo(VehicleInfo::class, 'vehicle_id');
    }
}
