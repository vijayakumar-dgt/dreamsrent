<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;

// use Modules\CarInfo\Database\Factories\VehicleFaqFactory;

class VehicleFaq extends Model
{

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
