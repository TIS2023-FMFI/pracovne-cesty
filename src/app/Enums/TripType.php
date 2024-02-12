<?php

namespace App\Enums;

enum TripType: int
{
    case DOMESTIC = 0;
    case FOREIGN = 1;

    public function inSlovak(): string
    {
        return match ($this) {
            self::DOMESTIC => 'Tuzemská',
            self::FOREIGN => 'Zahraničná'
        };
    }
}
