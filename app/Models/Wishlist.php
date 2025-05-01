<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CarInfo\Models\VehicleInfo;

class Wishlist extends Model
{
    protected $fillable = ['user_id','vehicle_id'];

    /**
     * @return BelongsTo<VehicleInfo, Wishlist>
     */
    public function vehicle(): BelongsTo
    {
        /** @var belongsTo<VehicleInfo, Wishlist> */
        return $this->belongsTo(VehicleInfo::class);
    }
}
