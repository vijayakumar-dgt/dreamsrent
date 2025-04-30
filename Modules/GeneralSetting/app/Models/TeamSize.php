<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\TeamSizeFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamSize extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name'];
}
