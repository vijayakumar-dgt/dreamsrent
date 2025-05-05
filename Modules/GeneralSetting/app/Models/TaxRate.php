<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Support\Carbon $created_at
 * @property string $created_on
 * @property float $tax_rate
 * @property string $tax_name
 */
class TaxRate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tax_name',
        'tax_rate',
        'status',
    ];

    /**
     * @return BelongsToMany<TaxGroup, TaxRate>
     */
    public function taxGroups(): BelongsToMany
    {
        /** @var belongsToMany<TaxGroup, TaxRate> */
        return $this->belongsToMany(TaxGroup::class, 'sub_taxes', 'tax_rate_id', 'tax_group_id');
    }
}
