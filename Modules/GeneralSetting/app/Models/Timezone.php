<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\GeneralSetting\Database\Factories\TimezoneFactory;

class Timezone extends Model
{
    use HasFactory;

    protected $table = "timezones";
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): TimezoneFactory
    // {
    //     // return TimezoneFactory::new();
    // }
}
