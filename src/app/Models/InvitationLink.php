<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InvitationLink extends Model
{
    protected $fillable = ['email', 'token', 'expires_at', 'used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Check whether a given token is valid
     *
     * @param string $token
     * @return bool
     */
    public static function isValid(string $token): bool
    {
        $link = self::where('token', $token)->first();
        return $link && !$link->used && Carbon::now() < $link->expires_at;
    }
}
