<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxGroup extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tax_name',
        'status',
    ];

    /**
     * @return BelongsToMany<TaxRate, TaxGroup>
     */
    public function taxRates(): BelongsToMany
    {
        /** @var belongsToMany<TaxRate, TaxGroup> */
        return $this->belongsToMany(TaxRate::class, 'sub_taxes', 'tax_group_id', 'tax_rate_id');
    }
}
