<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $insurance_id
 * @property string $benefit
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read Insurance $insurance
 */
class InsuranceBenefit extends Model
{
    protected $fillable = [
        'insurance_id',
        'benefit',
    ];

    /**
     * @return BelongsTo<Insurance, InsuranceBenefit>
     */
    public function insurance(): BelongsTo
    {
        /** @var BelongsTo<Insurance, InsuranceBenefit> */
        return $this->belongsTo(Insurance::class, 'insurance_id', 'id');
    }
}
