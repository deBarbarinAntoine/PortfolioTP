<?php

namespace App\Models;

/**
 * Enum ProjectRole
 *
 * Represents roles within a project, defining the level of access and permissions:
 * - `OWNER`: Full control over the project, including management of other roles and project settings.
 * - `CONTRIBUTOR`: Can contribute and make changes to the project but with limited administrative privileges.
 * - `VIEWER`: Read-only access to the project, without permissions to make any changes.
 */
enum ProjectRole: string
{
    case OWNER = 'owner';
    case CONTRIBUTOR = 'contributor';
    case VIEWER = 'viewer';
}