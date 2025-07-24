<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ReviewReaction
 *
 * @property int $id
 * @property int $user_id
 * @property int $review_id
 * @property bool $is_like
 * @property bool $is_dislike
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Review $review
 */
class ReviewReaction extends Model
{
    protected $fillable = [
        'user_id',
        'review_id',
        'is_like',
        'is_dislike',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_like' => 'boolean',
        'is_dislike' => 'boolean',
    ];

    /**
     * Get the user that owns the reaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the review that this reaction belongs to.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
