<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;

/**
 * @property Insurance $insurance
 * @property InsuranceBenefit $insuranceBenefits
 * @property int $vehicle_id
 * @property int $insurances_id
 * @property float $value
 * @property float $price
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */

class VehicleInsurance extends Model
{

    protected $table = 'vehicle_insurances';

    protected $fillable = ['vehicle_id', 'insurances_id', 'value', 'price'];

    /**
     * @return BelongsTo<Insurance, VehicleInsurance>
     */
    public function insurance(): BelongsTo
    {
        /** @var BelongsTo<Insurance, VehicleInsurance> */
        return $this->belongsTo(Insurance::class, 'insurances_id');
    }

    /**
     * @return HasMany<InsuranceBenefit, VehicleInsurance>
     */
    public function insuranceBenefits(): HasMany
    {
        /** @var HasMany<InsuranceBenefit, VehicleInsurance> */
        return $this->hasMany(InsuranceBenefit::class, 'insurance_id', 'insurances_id');
    }
}
