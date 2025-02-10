<?php

namespace App\Controllers;

use App\Models\ProjectUser;

class User_ProjectController
{

    public function getUserProject(int $user_id): array
    {
        if ($user_id >= 1)
        {
            return ProjectUser::getUserProject($user_id);
        }

        return [];
    }

    public function isUserAllowedToUpdate(string $projectId, mixed $userId): bool
    {
        $isOwner = ProjectUser::isOwner($projectId, $userId);
        $isContributor = ProjectUser::isContributor($projectId, $userId);
        if ($isOwner || $isContributor) {
            return true;
        }
        return false;
    }

    public function isUserAllowedToDelete(mixed $projectId, mixed $userId): bool
    {
       return ProjectUser::isOwner($projectId, $userId);
    }

    public function getIsOwner(string $projectId, mixed $userId): bool
    {
        return ProjectUser::isOwner($projectId, $userId);
    }

    public function addUserToProject(string $email, string $role, string $projectId): int
    {
        return ProjectUser::createAdd($email, $role, $projectId);
    }

    public function create(mixed $user_id, int $projectId): int
    {
        return ProjectUser::create($user_id, $projectId);
    }
}