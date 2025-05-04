<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

class SitemapUrl extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['url','sitemap_path'];
}
