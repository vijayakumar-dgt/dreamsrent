<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\GeneralSetting\Database\Factories\CurrencyFactory;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $table    = 'currencies'; 

    // protected static function newFactory(): CurrencyFactory
    // {
    //     // return CurrencyFactory::new();
    // }
}
