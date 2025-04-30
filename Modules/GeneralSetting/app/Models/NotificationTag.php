<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\GeneralSetting\Database\Factories\NotificationTagFactory;

class NotificationTag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $table = 'notification_tags';

    // protected static function newFactory(): NotificationTagFactory
    // {
    //     // return NotificationTagFactory::new();
    // }
}
