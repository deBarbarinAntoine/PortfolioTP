<?php

namespace App\Models;

/**
 * Enum GuardType
 *
 * Represents the different types of authorization guards used in the application:
 * - `PUBLIC`: Represents public access without any authentication required.
 * - `VISITOR`: Represents visitors who are not signed in but can access certain features.
 * - `USER`: Represents authenticated standard users with basic privileges.
 * - `ADMIN`: Represents administrators with full access and control over the application.
 */
enum GuardType: string
{
    case PUBLIC = 'PUBLIC'; // Public access with no authentication.
    case VISITOR = 'VISITOR'; // Visitors with limited non-authenticated access.
    case USER = 'USER'; // Authenticated users with standard privileges.
    case ADMIN = 'ADMIN'; // Administrators with full access rights.
}
