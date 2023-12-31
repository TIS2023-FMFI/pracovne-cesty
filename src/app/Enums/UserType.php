<?php

namespace App\Enums;

enum UserType: int
{
    case EMPLOYEE = 0;
    case PHD_STUDENT = 1;
    case STUDENT = 2;
    case EXTERN = 3;
    case ADMIN = 4;
}
