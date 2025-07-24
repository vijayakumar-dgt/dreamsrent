<?php

namespace Modules\Communication\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_id', 'user_id', 'description',
        'created_by', 'updated_by'
    ];

    /** @var array<int, string> */
    protected $dates = ['deleted_at'];

    /** @return BelongsTo<Ticket, TicketHistory> */
    public function ticket(): BelongsTo
    {
        /** @var BelongsTo<Ticket, TicketHistory> $relation */
        $relation = $this->belongsTo(Ticket::class, 'ticket_id');
        return $relation;
    }

    /** @return BelongsTo<User, TicketHistory> */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, TicketHistory> $relation */
        $relation = $this->belongsTo(User::class, 'user_id');
        return $relation;
    }

    /** @return BelongsTo<User, TicketHistory> */
    public function creator(): BelongsTo
    {
        /** @var BelongsTo<User, TicketHistory> $relation */
        $relation = $this->belongsTo(User::class, 'created_by');
        return $relation;
    }

    /** @return BelongsTo<User, TicketHistory> */
    public function updater(): BelongsTo
    {
        /** @var BelongsTo<User, TicketHistory> $relation */
        $relation = $this->belongsTo(User::class, 'updated_by');
        return $relation;
    }
}
