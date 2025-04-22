<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Communication\Models\Ticket;

class TicketHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id', 'user_id', 'description',
        'created_by', 'updated_by'
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
