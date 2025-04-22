<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;

// use Modules\CarInfo\Database\Factories\VehicleInsuranceFactory;

class VehicleInsurance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'vehicle_insurances';

    protected $fillable = ['vehicle_id', 'insurances_id', 'value', 'price'];


    // protected static function newFactory(): VehicleInsuranceFactory
    // {
    //     // return VehicleInsuranceFactory::new();
    // }
    public function insurance()
    {
        return $this->belongsTo(Insurance::class, 'insurances_id');
    }

    public function insuranceBenefits()
    {
        return $this->hasMany(InsuranceBenefit::class, 'insurance_id', 'insurances_id');
    }
}
