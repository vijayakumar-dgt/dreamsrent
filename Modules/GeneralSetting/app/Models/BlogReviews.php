<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $image
 * @property string $comment_date
 */
class BlogReviews extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'comments',
        'created_at',
        'updated_at',
        'blog_id',
    ];
}
