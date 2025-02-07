<?php

namespace App\Controllers;

use App\Models\Project;
use DateMalformedStringException;
use Exception;

class ProjectController {
    /**
     * @throws DateMalformedStringException
     */
    public function getPublicProjects(): array
    {
        return Project::getPublicProjects();
    }

    /**
     * @throws Exception
     */
    public function getProject(string $projectId, string $projectName): ?Project
    {
        return Project::getProject($projectId, $projectName);
    }

}

