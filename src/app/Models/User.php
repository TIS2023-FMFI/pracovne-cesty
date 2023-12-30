<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    use HasFactory;

    protected $connection = 'cesty';
    protected $table = 'users';

    protected $hidden = ['password'];
    protected $casts = [
        'user_type' => UserType::class,
        'status' => UserStatus::class,
        'last_login' => 'datetime'
    ];

    /**
     * Get the business trips associated with the user
     *
     * @return HasMany
     */
    public function businessTrips(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'user_id');
    }

    /**
     * Get the user with the same personal ID in the DB for Pritomnost
     * (may be none in case the user is not registered in Pritomnost)
     *
     * @return HasOne
     */
    public function pritomnostUser(): HasOne
    {
        return $this->hasOne(PritomnostUser::class, 'personal_id', 'personal_id');
    }
}
