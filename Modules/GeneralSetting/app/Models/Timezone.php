<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $name
 */
class Timezone extends Model
{
    protected $table = "timezones";
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
}