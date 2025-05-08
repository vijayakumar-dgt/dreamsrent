<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property string $key
 * @property string $value
 */
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
}
