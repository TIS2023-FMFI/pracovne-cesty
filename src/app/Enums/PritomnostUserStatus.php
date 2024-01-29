<?php

namespace App\Enums;

enum PritomnostUserStatus: int
{
    case DISABLED = 0;
    case REGULAR = 1;
    case SECRETARY = 2;
    case ADMIN = 3;
}
