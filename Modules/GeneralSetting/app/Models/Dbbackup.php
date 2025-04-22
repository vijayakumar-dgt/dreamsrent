<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\DbbackupFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dbbackup extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','type'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dbbackups';
}
