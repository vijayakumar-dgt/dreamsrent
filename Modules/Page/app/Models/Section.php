<?php

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Section Model
 *
 * @property int $id
 * @property int|null $theme_id
 * @property string|null $name
 * @property int|null $status
 * @property string|null $datas
 * @property string|null $content
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 */
class Section extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ["name", "theme_id", "content", "status", "datas", "icon"];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'datas' => 'array',
        'status' => 'integer',
        'theme_id' => 'integer',
    ];

}
