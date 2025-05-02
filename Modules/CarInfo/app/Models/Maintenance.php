<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\MaintananceFactory;
/**
 * @property int $id
 * @property int $vehicle_id
 * @property string $odometer
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $details
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string|array<string>|null $vehicle_image
 * @property string|null $status_text
 */
class Maintenance extends Model
{
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


    public static int $planned = 1;
    public static int $inprogress = 2;
    public static int $completed = 3;
    /**
     *  @return HasOne<VehicleInfo, Maintenance>
     */
    public function car(): HasOne
    {
        /** @var HasOne<VehicleInfo,Maintenance> */
        return $this->hasOne(VehicleInfo::class, 'id');
    }
    /**
     *  @return BelongsTo<VehicleInfo, Maintenance>
     */
    public function vehicle(): BelongsTo
    {  
        /** @var BelongsTo<VehicleInfo,Maintenance> */
        return $this->belongsTo(VehicleInfo::class, 'vehicle_id');
    }
}
