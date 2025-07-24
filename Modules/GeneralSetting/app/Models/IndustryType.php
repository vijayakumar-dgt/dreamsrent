<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndustryType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];
}
