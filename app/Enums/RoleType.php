<?php

namespace App\Enums;

enum RoleType: int
{
    case MANEGER = 0;
    case ADMIN = 1;
    case USER = 2;
}
