<?php

namespace App\Enums;

enum TripState: int
{
    case NEW = 0;
    case CONFIRMED = 1;
    case COMPLETED = 2;
    case CLOSED = 3;
    case CANCELLATION_REQUEST = 4;
    case CANCELLED = 5;
}
