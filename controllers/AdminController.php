<?php

namespace controllers;
use models\admin;

require_once __DIR__ . '/../models/admin.php';

class AdminController {
    public function getAdminDashboard(): ?array
    {
        return admin::getAdminDashboard();
    }
}