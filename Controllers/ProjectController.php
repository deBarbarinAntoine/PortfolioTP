<?php

namespace App\Controllers;

use App\Models\Project;

class ProjectController {
    public function getPublicProjects(): array
    {
        return Project::getPublicProjects();
    }
}

