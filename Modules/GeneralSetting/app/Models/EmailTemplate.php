<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @property string|null $subject
 * @property string|null $description
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
