<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Language extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * @return BelongsTo<TranslationLanguage, Language>
     */
    public function transLang(): BelongsTo
    {
        /** @var BelongsTo<TranslationLanguage, Language> */
        return $this->belongsTo(TranslationLanguage::class, 'language_id', 'id');
    }
}
