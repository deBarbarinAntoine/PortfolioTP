<?php

namespace App\Controllers;
use DateMalformedStringException;
use App\Models\Admin;

require_once __DIR__ . '/../Models/Admin.php';

class AdminController {
    public function getAdminDashboard(): ?array
    {
        return Admin::getAdminDashboard();
    }

    /**
     * @throws DateMalformedStringException
     */
    public function get_admin_users(string $search, int $offset): array
    {
        return Admin::get_admin_users($search,$offset);
    }

    public function deleteUser($user_id): bool
    {

        return Admin::deleteUser($user_id);
    }

    public function get_admin_skills(mixed $search, mixed $offset): array
    {
        return Admin::get_admin_skills($search,$offset);
    }

}