<?php

namespace controllers;
use models\project;

require_once __DIR__ . '/../models/Project.php';

class ProjectController {
    public function getPublicProjects() {
        return project::getPublicProjects();
    }
}

