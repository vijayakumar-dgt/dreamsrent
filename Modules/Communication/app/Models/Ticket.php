<?php

namespace Modules\Communication\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modules\Communication\Models\Ticket
 *
 * @property int $id
 * @property string $ticket_id
 * @property string $priority
 * @property int $user_id
 * @property int $subject
 * @property string $description
 * @property int $user_type
 * @property int $status
 * @property string|null $reply_description
 * @property string|null $attachment
 * @property int|null $assignee_id
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User $user
 * @property-read User|null $assignee
 * @property-read User|null $creator
 * @property-read User|null $updater
 * @property-read TicketCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TicketHistory> $ticketHistories
 */
class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_id', 'priority', 'user_id', 'subject', 'description',
        'user_type', 'status', 'reply_description', 'attachment',
        'assignee_id', 'created_by', 'updated_by'
    ];

    /** @var array<int, string> */
    protected $dates = ['deleted_at'];

    // @phpstan-ignore-next-line
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // @phpstan-ignore-next-line
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    // @phpstan-ignore-next-line
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // @phpstan-ignore-next-line
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // @phpstan-ignore-next-line
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'subject', 'id');
    }

    // @phpstan-ignore-next-line
    public function ticketHistories(): HasMany
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id', 'id');
    }
}
