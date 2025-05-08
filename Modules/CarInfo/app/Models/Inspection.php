<?php

namespace Modules\CarInfo\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $vehicle_info_id
 * @property string $inspection_date
 * @property double $odometer
 * @property int $inspector_id
 * @property double $fuel
 * @property string $notes
 * @property string $inspection_status
 * @property string $repair_status
 * @property string|null $check_list
 * @property string|null $inspectiondate
 */
class Inspection extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     *  @return BelongsTo<VehicleInfo,Inspection>
     */
    public function car(): BelongsTo
    {
        /** @var BelongsTo<VehicleInfo,Inspection>  */
        return $this->belongsTo(VehicleInfo::class, 'vehicle_info_id', 'id');
    }
    /**
     * @return BelongsTo<User,Inspection>
     */
    public function inspector(): BelongsTo
    {
        /** @var BelongsTo<User,Inspection> */
        return $this->belongsTo(User::class, 'inspector_id', 'id');
    }
}
