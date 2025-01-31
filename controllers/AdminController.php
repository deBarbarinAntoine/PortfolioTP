<?php

namespace controllers;
use DateMalformedStringException;
use models\admin;

require_once __DIR__ . '/../models/admin.php';

class AdminController {
    public function getAdminDashboard(): ?array
    {
        return admin::getAdminDashboard();
    }

    /**
     * @throws DateMalformedStringException
     */
    public function get_admin_users(string $search, int $offset): array
    {
        return admin::get_admin_users($search,$offset);
    }

    public function deleteUser($user_id): bool
    {

        return admin::deleteUser($user_id);
    }

}