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
        'email', 'username', 'password', 'status',
        'iban', 'spp_user_type', 'personal_id_dochadzka'
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
        if ($this->user_type !== UserType::EMPLOYEE) {
            return $this->hasOne(PritomnostUser::class, 'personal_id', 'personal_id_dochadzka');
        }
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

    public static function getSortedByLastName() : Collection {
        return self::orderBy('last_name', 'asc')->get();
    }


    public static function updateIbanOfUserWithId($id, $newIban):bool
    {
        $affectedRows = self::where('id', $id)->update(["iban" => $newIban]);
        return $affectedRows > 0;
    }

    public static function activateUserWithId($id):bool
    {
        $affectedRows = self::where("id", $id)->update(["status" => UserStatus::ACTIVE]);
        return $affectedRows > 0;
    }

    public static function deactivateUserWithId($id):bool
    {
        $affectedRows = self::where("id", $id)->update(["status" => UserStatus::INACTIVE]);
        return $affectedRows > 0;
    }

    public static function getFullNameOfUserWithID($id):string{
        $user = self::where("id", $id)->first();
        if($user == null){
            return '';
        }
        $degrees = '';
        if($user->academic_degrees !== null){
            $degrees = $user->academic_degrees;
        }
        return $degrees . ' ' . $user->first_name . ' ' . $user->last_name;
    }
}
