<?php

namespace Modules\CarInfo\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\InspectionFactory;

class Inspection extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): InspectionFactory
    // {
    //     // return InspectionFactory::new();
    // }
    public function car()
    {
        return $this->belongsTo(VehicleInfo::class, 'vehicle_info_id', 'id');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id', 'id');
    }
}
