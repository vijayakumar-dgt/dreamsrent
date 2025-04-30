<?php

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Page\Database\Factories\PageFactory;

class Page extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'theme_id',
        'parent_id',
        'read',
        'page_title',
        'slug',
        'page_content',
        'seo_tag',
        'seo_title',
        'seo_description',
        'keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'language_id',
        'status',
        'created_at',
    ];

    // protected static function newFactory(): PageFactory
    // {
    //     // return PageFactory::new();
    // }
}
