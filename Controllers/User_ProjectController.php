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
}