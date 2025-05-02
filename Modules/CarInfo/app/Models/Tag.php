<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\TagFactory;
/**
 * @property int $id
 * @property string $tag
 * @property int $status
 */
class Tag extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): TagFactory
    // {
    //     // return TagFactory::new();
    // }
}
