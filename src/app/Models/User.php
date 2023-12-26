<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;

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
}
