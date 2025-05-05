<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\CommunicationSettingFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CommunicationSetting
 *
 * @property string|null $type
 * @property string $key
 * @property mixed $value
 * @property int $settings_type
 * @property int|null $created_by
 * @property int|null $updated_by
 */
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
