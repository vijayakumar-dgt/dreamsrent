<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\NotificationTypeFactory;

class NotificationType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $table    = "notification_types";

    // protected static function newFactory(): NotificationTypeFactory
    // {
    //     // return NotificationTypeFactory::new();
    // }
}
