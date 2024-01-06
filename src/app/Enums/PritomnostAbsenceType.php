<?php

namespace App\Enums;

enum PritomnostAbsenceType: int
{
    case SICK = 1;
    case BUSINESS_TRIP = 2;
    case VACATION = 3;
    case HOME_OFFICE = 4;
    case OTHER = 5;
    case MATERNITY = 6;
    case PARENTAL = 7;
}
