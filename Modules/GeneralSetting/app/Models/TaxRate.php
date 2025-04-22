<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\GeneralSetting\Database\Factories\TaxRateFactory;

class TaxRate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tax_name',
        'tax_rate',
        'status',
    ];

    public function taxGroups()
    {
        return $this->belongsToMany(TaxGroup::class, 'sub_taxes', 'tax_rate_id', 'tax_group_id');
    }

}
