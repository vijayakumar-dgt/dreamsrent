<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $reply_date
 * @property string|array<string>|null $profile_image
 * @property string|null $full_name
 * @property string|null $user_name
 */
class ReviewMessages extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'review_id',
        'parent_id',
        'user_id',
        'comments',
        'likes',
        'dislikes',
    ];
}
