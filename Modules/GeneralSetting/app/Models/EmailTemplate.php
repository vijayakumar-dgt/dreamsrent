<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

 /**
 * @property int $id
 * @property string $title
 * @property string $notification_type
 * @property string|null $subject
 * @property string $sms_content
 * @property string|null $description
 * @property int $status
 * @property string $notification_content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EmailTemplate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $table    = 'email_templates';
}
