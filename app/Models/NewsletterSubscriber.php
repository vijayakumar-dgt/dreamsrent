<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $created_date
 */

class NewsletterSubscriber extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'email',
    ];
}
