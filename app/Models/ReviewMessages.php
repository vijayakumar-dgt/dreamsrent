<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
