<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceBenefit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
