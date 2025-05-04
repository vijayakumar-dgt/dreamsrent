<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'banks';
    protected $fillable = ['bank_name', 'account_number', 'account_holder_name', 'branch', 'ifsc', 'default'];
}
