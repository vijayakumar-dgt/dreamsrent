<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\GeneralSetting\Database\Factories\TranslationLanguageFactory;

class TranslationLanguage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): TranslationLanguageFactory
    // {
    //     // return TranslationLanguageFactory::new();
    // }
}
