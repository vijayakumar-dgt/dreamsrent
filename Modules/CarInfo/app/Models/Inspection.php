<?php

namespace Modules\CarInfo\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\InspectionFactory;

class Inspection extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): InspectionFactory
    // {
    //     // return InspectionFactory::new();
    // }
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
