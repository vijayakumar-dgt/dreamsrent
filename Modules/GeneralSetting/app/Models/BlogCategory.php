<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int|null $parent_id
 * @property string $name
 */
class BlogCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'status', 'created_at', 'updated_at', 'language_id', 'parent_id'];

    /**
     * @return HasMany<BlogPost, BlogCategory>
     */
    public function posts(): HasMany
    {
        /** @var HasMany<BlogPost, BlogCategory> */
        return $this->hasMany(\Modules\GeneralSetting\Models\BlogPost::class, 'category');
    }
}
