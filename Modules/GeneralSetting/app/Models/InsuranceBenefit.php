<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\GeneralSetting\Database\Factories\InsuranceBenefitFactory;

class InsuranceBenefit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'insurance_id',
        'benefit',
    ];

    public function insurance()
    {
        return $this->belongsTo(Insurance::class, 'id');
    }
}
