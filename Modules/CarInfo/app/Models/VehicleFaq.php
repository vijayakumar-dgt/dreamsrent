<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CarInfo\Database\Factories\VehicleFaqFactory;

class VehicleFaq extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'vehicle_faqs';
    protected $fillable = [ 'vehicle_id', 'question', 'answer'  ];

    // protected static function newFactory(): VehicleFaqFactory
    // {
    //     // return VehicleFaqFactory::new();
    // }
}
