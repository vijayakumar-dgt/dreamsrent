<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ReviewMessages Model
 *
 * @property int $id
 * @property int $review_id
 * @property int $parent_id
 * @property int $user_id
 * @property string $comments
 * @property int $likes
 * @property int $dislikes
 * @property-read \App\Models\Review $review
 * @property-read \App\Models\User $user
 * @property-read ReviewMessages|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|ReviewMessages[] $children
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

    /**
     * Get the review this message belongs to.
     */
    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }

    /**
     * Get the user who wrote this message.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the parent message (if this is a reply).
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the replies to this message.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
