<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\TimeFormatFactory;

class TimeFormat extends Model
{
    use HasFactory;

    protected $table = "time_formats";
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): TimeFormatFactory
    // {
    //     // return TimeFormatFactory::new();
    // }
}
