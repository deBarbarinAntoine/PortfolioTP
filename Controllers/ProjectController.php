<?php

namespace App\Controllers;

use App\Models\Project;

class ProjectController {
    public function getPublicProjects() {
        return Project::getPublicProjects();
    }
}

