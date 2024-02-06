<?php

namespace App\Enums;

enum UserType: int
{
    case EMPLOYEE = 0;
    case PHD_STUDENT = 1;
    case STUDENT = 2;
    case EXTERN = 3;
    case ADMIN = 4;

    /**
     * Returns internal access role associated with the user type
     *
     * @return string
     */
    public function role(): string
    {
        return match ($this) {
            self::EMPLOYEE, self::PHD_STUDENT, self::STUDENT, self::EXTERN => 'traveller',
            self::ADMIN => 'admin',
        };
    }

    /**
     * Decides if user type is external to the faculty
     *
     * @return bool
     */
    public function isExternal(): bool
    {
        return match ($this) {
            self::EMPLOYEE, self::PHD_STUDENT, self::ADMIN => false,
            self::STUDENT, self::EXTERN => true,
        };
    }
}
