<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NewsletterSubscriber
 *
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class NewsletterSubscriber extends Model
{
    use SoftDeletes;

    // If your table name is not 'newsletter_subscribers', uncomment and set it:
    // protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
    ];

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
