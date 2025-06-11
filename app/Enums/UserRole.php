<?php

namespace App\Enums;

enum UserRole: string
{
    case DRIVER = 'driver';
    case OPERATOR = 'operator';
    case MANAGER = 'manager';
    case ADMIN = 'admin';
}
