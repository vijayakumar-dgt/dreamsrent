<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Communication\Database\Factories\AnnouncementFactory;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'announcement_title',
        'announcement_type',
        'user_type',
        'status',
        'description'
    ];
}
