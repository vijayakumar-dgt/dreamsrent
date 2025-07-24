<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamSize extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];
}
