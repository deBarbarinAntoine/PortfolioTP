<?php

use models\Project;

require_once __DIR__ . '/../models/Project.php';

class ProjectController {
    public function getPublicProjects() {
        return Project::getPublicProjects();
    }
}

