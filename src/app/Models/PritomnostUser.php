<?php

namespace App\Models;

use App\Enums\PritomnostUserStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PritomnostUser extends Model
{
    private const REQUEST_VALIDATOR_ID = 822;   # Udaj z PritomnostNaPracovisku/Aplikácia/include/config.php
    protected $connection = 'dochadzka';
    protected $table = 'users';

    public $timestamps = false;

    protected $hidden = ['password'];
    protected $casts = [
        'personal_id' => 'string',
        'status' => PritomnostUserStatus::class,
        'last_login' => 'datetime'
    ];

    /**
     * Get the absences associated with the user
     *
     * @return HasMany
     */
    public function absences(): HasMany
    {
        return $this->hasMany(PritomnostAbsence::class, 'user_id');
    }

    /**
     * Get the user with the same personal ID in the DB for Cesty
     *
     * @return HasOne
     */
    public function cestyUser(): HasOne
    {
        $relationship = $this->hasOne(User::class, 'personal_id', 'personal_id');
        if ($relationship !== null) {
            return $relationship;
        }
        
        return $this->hasOne(User::class, 'personal_id_dochadzka', 'personal_id');
    }

    public static function getRequestValidators() {
        return self::where('personal_id', self::REQUEST_VALIDATOR_ID)->get();
    }
}
