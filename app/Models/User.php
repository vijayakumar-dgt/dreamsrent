<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Booking\Models\Booking;
use App\Models\UserDetail;

/**
 * @property int|null $language_id
 * @property string|null $customer_full_name
 * @property string|array<string>|null $profile_image
 * @property string|null $language_code
 * @property string|null $language_flag
 * @property string|null $encrypted_id
 * @property string|null $valid_date
 * @property string|null $date_of_issue
 * @property string|null $dob
 * @property string|null $added_on
 * @property int|null $id
 * @property \Carbon\Carbon|null $last_password_changed_at
 * @property string|null $password
 * @property string|null $first_name
 * @property string|null $last_name
 *
 */

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'user_type',
        'fcm_token',
        'status',
        'region_id',
        'language_id',
        'role_id',
        'last_password_changed_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static string $userSecretKey = 'userId';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * @return HasOne<UserDetail, User>
     */
    public function userDetail(): HasOne
    {
        /** @var hasOne<UserDetail, User> */
        return $this->hasOne(UserDetail::class, 'user_id');
    }

    /**
     * @return HasMany<UserDetail, User>
     */
    public function documents(): HasMany
    {
        /** @var hasMany<UserDetail, User> */
        return $this->hasMany(UserDocument::class, 'user_id');
    }

    /**
     * @return HasOne<UserDetail, User>
     */
    public function userDetails(): HasOne
    {
        /** @var hasOne<UserDetail, User> */
        return $this->hasOne(UserDetail::class, 'user_id');
    }

    /**
     * @return HasMany<UserDetail, User>
     */
    public function bookings(): HasMany
    {
        /** @var hasMany<UserDetail, User> */
        return $this->hasMany(Booking::class, 'customer_id', 'id');
    }
}
