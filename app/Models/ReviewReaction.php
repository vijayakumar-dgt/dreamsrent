<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReaction extends Model
{
    protected $fillable = [
        'user_id',
        'review_id',
        'is_like',
        'is_dislike',
    ];
}
