<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;

class VehicleInsurance extends Model
{

    protected $table = 'vehicle_insurances';

    protected $fillable = ['vehicle_id', 'insurances_id', 'value', 'price'];

    /**
     * @return BelongsTo<Insurance, VehicleInsurance>
     */
    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class, 'insurances_id');
    }

    /**
     * @return HasMany<InsuranceBenefit, VehicleInsurance>
     */
    public function insuranceBenefits(): HasMany
    {
        return $this->hasMany(InsuranceBenefit::class, 'insurance_id', 'insurances_id');
    }
}
