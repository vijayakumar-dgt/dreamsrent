<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\GeneralSetting\Database\Factories\TaxGroupFactory;

class TaxGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tax_name',
        'status',
    ];

    public function taxRates()
    {
        return $this->belongsToMany(TaxRate::class, 'sub_taxes', 'tax_group_id', 'tax_rate_id');
    }
}
