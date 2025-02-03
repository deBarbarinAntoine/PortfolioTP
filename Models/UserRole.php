<?php

namespace App\Models;

/**
 * Enum representing user roles in the application.
 *
 * This enum defines the following roles:
 * - `USER`: Represents a regular user.
 * - `ADMIN`: Represents an administrator with elevated permissions.
 */
enum UserRole: string
{
    case USER = 'user';   // Represents a regular user.
    case ADMIN = 'admin'; // Represents an administrator with elevated permissions.
}
