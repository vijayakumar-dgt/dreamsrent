<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Language Model
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
 * @property int|null $default
 * @property string|null $rtl
 */
class Language extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['language_id','rtl','default','status'];

    /**
     * @return BelongsTo<TranslationLanguage, Language>
     */
    public function transLang(): BelongsTo
    {
        /** @var BelongsTo<TranslationLanguage, Language> */
        return $this->belongsTo(TranslationLanguage::class, 'language_id', 'id');
    }
}
