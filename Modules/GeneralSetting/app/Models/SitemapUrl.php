<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\SitemapUrlFactory;

class SitemapUrl extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['url','sitemap_path'];

    // protected static function newFactory(): SitemapUrlFactory
    // {
    //     // return SitemapUrlFactory::new();
    // }
}
