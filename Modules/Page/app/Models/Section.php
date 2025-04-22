<?php

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Page\Database\Factories\SectionFactory;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = "sections";

    protected $fillable = ["name", "theme_id", "content", "status", "datas"];


    // protected static function newFactory(): SectionFactory
    // {
    //     // return SectionFactory::new();
    // }
}
