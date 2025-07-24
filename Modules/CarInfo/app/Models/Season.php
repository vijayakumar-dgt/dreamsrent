<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $status
 */
class Season extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
}
