<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $location_id
 * @property string $day
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int $status
 */
class LocationWorkingDay extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
}
