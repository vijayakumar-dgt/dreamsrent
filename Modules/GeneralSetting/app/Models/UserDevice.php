<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

/** 
 *  @property int|null $user_id
 *  @property string|null $device_type
 *  @property string|null $browser
 *  @property string|null $os
 *  @property string|null $ip_address
 *  @property string|null $location
 */
class UserDevice extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
}
