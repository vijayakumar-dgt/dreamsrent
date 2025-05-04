<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunicationSetting extends Model
{
    use SoftDeletes;

    protected $table = 'communication_settings';

    protected $fillable = [
        'type',
        'key',
        'value',
        'settings_type',
        'created_by',
        'updated_by'
    ];
}
