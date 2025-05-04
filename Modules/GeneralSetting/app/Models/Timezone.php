<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    protected $table = "timezones";
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
}