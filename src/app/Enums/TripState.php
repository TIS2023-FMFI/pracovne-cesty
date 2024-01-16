<?php

namespace App\Enums;

enum TripState: int
{
    case NEW = 0;
    case CONFIRMED = 1;
    case UPDATED = 2;
    case COMPLETED = 3;
    case CLOSED = 4;
    case CANCELLATION_REQUEST = 5;
    case CANCELLED = 6;
}
