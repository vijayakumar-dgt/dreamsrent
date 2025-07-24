<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TranslationLanguage Model
 *
 * @property int $id
 * @property int $language_id
 * @property string $name
 * @property string $code
 * @property string $flag
 * @property string $direction
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TranslationLanguage extends Model
{
    protected $fillable = [];
}
