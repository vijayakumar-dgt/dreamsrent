<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'general_settings';
    protected $fillable = ['key', 'value', 'group_id', 'language_id'];
}
