<?php

namespace App\Models;

use App\Enums\PritomnostUserStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PritomnostUser extends Model
{
    protected $connection = 'pritomnost';
    protected $table = 'users';

    public $timestamps = false;

    protected $hidden = ['password'];
    protected $casts = [
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
}
