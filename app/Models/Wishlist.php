<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\CarInfo\Models\VehicleInfo;

class Wishlist extends Model
{
    protected $fillable = ['user_id','vehicle_id'];

    public function vehicle()
    {
        return $this->belongsTo(VehicleInfo::class);
    }
}
