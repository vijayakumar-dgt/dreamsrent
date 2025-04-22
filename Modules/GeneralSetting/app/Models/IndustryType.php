<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\IndustryTypeFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class IndustryType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];
}
