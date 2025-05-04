<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CarInfo\Models\PricingType;

class Insurance extends Model
{
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

    /**
     * @return HasMany<InsuranceBenefit, Insurance>
     */
    public function insuranceBenefits(): HasMany
    {
        /** @var HasMany<InsuranceBenefit, Insurance> */
        return $this->hasMany(InsuranceBenefit::class, 'insurance_id');
    }

    /**
     * @return BelongsTo<PricingType, Insurance>
     */
    public function priceType(): BelongsTo
    {
        /** @var BelongsTo<PricingType, Insurance> */
        return $this->belongsTo(PricingType::class, 'price_type_id');
    }
}
