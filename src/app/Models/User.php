<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, CanResetPassword;

    protected $connection = 'cesty';
    protected $table = 'users';

    protected $hidden = ['password'];
    protected $casts = [
        'user_type' => UserType::class,
        'status' => UserStatus::class,
        'last_login' => 'datetime'
    ];

    // Mass assignable attributes
    protected $fillable = [
        'first_name', 'last_name', 'academic_degrees',
        'personal_id', 'department', 'address',
        'email', 'username', 'password', 'status'
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

    public function isAdmin(): bool
    {
        return UserType::from($this->user_type) === UserType::ADMIN;
    }

    /**
     * Get email addresses of registered admins
     *
     * @return Collection
     */
    public static function getAdminEmails(): Collection
    {
        return self::where('user_type',  UserType::ADMIN->value)->pluck('email');
    }

    public function fullName(): string {
        return $this->first_name . ' ' . $this->last_name;
    }
}
