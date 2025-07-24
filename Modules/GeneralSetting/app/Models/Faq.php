<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes;

    protected $fillable = ['order_by', 'question', 'answer', 'status', 'language_id', 'parent_id'];

    /**
     * @return BelongsTo<Language, Faq>
     */
    public function language(): BelongsTo
    {
        /** @var BelongsTo<Language, Faq> */
        return $this->belongsTo(Language::class);
    }

    /**
     * @return BelongsTo<Faq, Faq>
     */
    public function parent(): BelongsTo
    {
        /** @var BelongsTo<Faq, Faq> */
        return $this->belongsTo(Faq::class, 'parent_id');
    }

    /**
     * @return HasMany<Faq, Faq>
     */
    public function children(): HasMany
    {
        /** @var HasMany<Faq, Faq> */
        return $this->hasMany(Faq::class, 'parent_id');
    }
}
