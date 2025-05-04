<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $table    = "notification_types";
}
