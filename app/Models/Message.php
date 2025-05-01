<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
     /**
     * @return BelongsTo<User, Message>
     */
    public function sender(): BelongsTo
    {
         /** @var BelongsTo<User, Message> */
        return $this->belongsTo(User::class, 'sender_id');
    }

     /**
     * @return BelongsTo<User, Message>
     */
    public function receiver(): BelongsTo
    {
         /** @var BelongsTo<User, Message> */
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
