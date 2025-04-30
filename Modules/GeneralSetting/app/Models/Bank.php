<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\GeneralSetting\Database\Factories\BankFactory;

class Bank extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'banks';
    protected $fillable = ['bank_name', 'account_number', 'account_holder_name', 'branch', 'ifsc', 'default'];

    // protected static function newFactory(): BankFactory
    // {
    //     // return BankFactory::new();
    // }
}
