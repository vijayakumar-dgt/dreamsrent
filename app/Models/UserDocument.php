<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserDocument> $documents
 */

class UserDocument extends Model
{
    protected $fillable = [
        'user_id',
        'document'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
