<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\IndustryTypeFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class IndustryType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name'];
}
