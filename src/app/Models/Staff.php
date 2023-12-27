<?php

namespace App\Models;

use App\Enums\PositionTitle;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $casts = [
        'position' => PositionTitle::class
    ];
}
