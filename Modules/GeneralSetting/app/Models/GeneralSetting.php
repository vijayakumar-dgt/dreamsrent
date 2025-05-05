<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $key
 * @property string|null $value
 */
class GeneralSetting extends Model
{
    use SoftDeletes;

    protected $table = 'general_settings';
    protected $fillable = ['key', 'value', 'group_id', 'language_id'];
}
