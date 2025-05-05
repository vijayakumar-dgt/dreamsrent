<?php

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Page\Database\Factories\SectionFactory;

/**
 * @property int $id
 * @property int|null $theme_id
 * @property string|null $name
 * @property int|null $status
 * @property string|null $datas
 */
class Section extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = "sections";

    protected $fillable = ["name", "theme_id", "content", "status", "datas"];


}
