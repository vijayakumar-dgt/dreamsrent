<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone_number
 * @property string|null $message
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @extends \Illuminate\Database\Eloquent\Model<\Modules\Communication\Models\Contact>
 */

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'message',
        'image',
    ];
}
