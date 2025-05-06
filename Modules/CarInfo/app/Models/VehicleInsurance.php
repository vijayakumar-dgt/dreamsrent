<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;
use Illuminate\Support\Collection;

/**
 * @property Insurance $insurance
 * @property Collection<int, InsuranceBenefit> $insuranceBenefits
 * @property int $vehicle_id
 * @property int $insurances_id
 * @property float $value
 * @property float $price
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * 
 * @property int $benefits_count
 * @property string $first_benefit
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
        return $this->belongsTo(Insurance::class, 'insurances_id');
    }

    /**
     * @return HasMany<InsuranceBenefit>
     */
    public function insuranceBenefits(): HasMany
    {
        return $this->hasMany(InsuranceBenefit::class, 'insurance_id', 'insurances_id');
    }
}
