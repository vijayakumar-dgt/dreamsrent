<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Notification
 *
 * @property int $id
 * @property int $user_id
 * @property string $subject
 * @property string $content
 * @property bool $readed
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Notification extends Model
{
    use SoftDeletes;

    protected $table = "notifications";
    protected $fillable = ['user_id','subject','content','readed','read_at'];

    protected $casts = [
        'readed' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected $attributes = [
        'readed' => false,
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('readed', false);
    }
}
