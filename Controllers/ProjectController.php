<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Visibility;
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
    public function getProject(string $projectId): ?Project
    {
        return Project::getProject($projectId);
    }

    public function createProject(string $title, string $description, string $externalLink, string $visibility): int
    {
        return Project::createProject($title, $description, $externalLink, $visibility);
    }

    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function modifyProject(
        string $projectId,
        string $title,
        string $description,
        string $externalLink,
        string $visibility,
        array $images
    ): int {
        // Fetch the existing project
        $project = Project::get($projectId);

        // Check if project exists
        if (!$project) {
            throw new Exception("Project with ID $projectId not found.");
        }

        // Update project properties
        $project->setTitle($title);
        $project->setDescription($description);
        $project->setExternalLink($externalLink); // Ensure correct property name
        $project->setVisibility(Visibility::tryFrom($visibility) ?? Visibility::PRIVATE);
        $project->setImages($images);

        // Perform update and return status
        return $project->update();
    }

    public function deleteProject(mixed $projectId): int
    {
      return Project::delete($projectId);
    }

}

