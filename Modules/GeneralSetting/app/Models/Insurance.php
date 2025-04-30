<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CarInfo\Models\PricingType;

// use Modules\GeneralSetting\Database\Factories\InsuranceFactory;

class Insurance extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'language_id',
        'insurance_name',
        'price',
        'price_type_id',
        'status'
    ];

    public function insuranceBenefits()
    {
        return $this->hasMany(InsuranceBenefit::class, 'insurance_id');
    }

    public function priceType()
    {
        return $this->belongsTo(PricingType::class, 'price_type_id');
    }
}
