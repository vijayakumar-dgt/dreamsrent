<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['order_by', 'question', 'answer', 'status', 'language_id', 'parent_id'];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function parent()
    {
        return $this->belongsTo(Faq::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Faq::class, 'parent_id');
    }
}
