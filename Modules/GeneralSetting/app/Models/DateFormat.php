<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\DateFormatFactory;

class DateFormat extends Model
{
    use HasFactory;

    protected $table = 'date_formats';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): DateFormatFactory
    // {
    //     // return DateFormatFactory::new();
    // }
}
