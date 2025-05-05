<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Communication\Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Communication\Models\TicketCategory;
use Modules\Communication\Models\TicketHistory;
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

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id')->with('userDetail');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'subject', 'id');
    }
    public function ticketHistories()
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id','id');
    }
}
