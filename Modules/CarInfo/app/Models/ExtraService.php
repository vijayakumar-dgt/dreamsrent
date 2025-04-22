<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\ExtraServiceFactory;

class ExtraService extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ["name", "language_id", "icon", "description", "image"];

    // protected static function newFactory(): ExtraServiceFactory
    // {
    //     // return ExtraServiceFactory::new();
    // }
}
