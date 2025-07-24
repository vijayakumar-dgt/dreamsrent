<?php

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int|null $theme_id
 * @property int|null $parent_id
 * @property int|null $language_id
 * @property string|null $page_title
 * @property string|null $page_content
 * @property string|null $slug
 * @property int|null $read
 * @property string|null $status
 * @property string|null $seo_tag
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $keywords
 * @property string|null $canonical_url
 * @property string|null $og_title
 * @property string|null $og_description
 * @property \Carbon\Carbon|null $created_at
 */


class Page extends Model
{
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
}
