<?php

namespace App\Models;

/**
 * Enum Visibility
 *
 * Represents the visibility status of an entity.
 * - `PRIVATE`: The entity is only visible to the owner or restricted users.
 * - `PUBLIC`: The entity is visible to everyone without restrictions.
 */
enum Visibility: string
{
    case PRIVATE = 'private';
    case PUBLIC = 'public';
}
