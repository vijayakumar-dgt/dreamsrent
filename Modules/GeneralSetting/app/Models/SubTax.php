<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

class SubTax extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tax_group_id',
        'tax_rate_id',
    ];
}
