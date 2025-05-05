<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
   /**
    * The attributes that are mass assignable.
    */
   protected $table = 'addons';

   protected $fillable = [
      'name',
      'status',
      'slug',
      'version',
      'price',
      'created_at',
      'updated_at',
      'deleted_at'
   ];
}
