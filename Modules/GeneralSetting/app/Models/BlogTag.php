<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogTag extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'status', 'created_at', 'updated_at', 'language_id', 'parent_id'];

    /**
     * @return HasMany<BlogPost, BlogTag>
     */
    public function posts(): HasMany
    {
        /** @var HasMany<BlogPost, BlogTag> */
        return $this->hasMany(\Modules\GeneralSetting\Models\BlogPost::class, 'category');
    }
}
