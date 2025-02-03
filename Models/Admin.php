<?php

namespace App\Models;

use DateMalformedStringException;
use PDOException;

class Admin
{
    public static function getAdminDashboard()
    {
        try {
            $users_count = User::getCountAll();
            $users_count_last24hours = User::getCountLastUsers();
            $latest_users = User::get5LastUsers();
            $skills_count = Skill::getCountAll();
            $skills = Skill::getAllSkills();
            $projects_count = Project::getCountAll();
            $projects_count_last24hours = Project::getCountLastProject();

            return [
                'users_count' => $users_count,
                'skills_count' => $skills_count,
                'projects_count' => $projects_count,
                'users_count_last24hours' => $users_count_last24hours,
                'projects_count_last24hours' => $projects_count_last24hours,
                'latest_users' => $latest_users,
                'skills' => $skills
            ];
        } catch (PDOException|DateMalformedStringException $e) {

            // LOGGING
            Logger::log("Error fetching data: " . $e->getMessage(), __METHOD__);

            die("Error fetching data: " . $e->getMessage());
        }
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function get_admin_users(string $search, int $offset): array
    {
        return User::getAllUsers($search, $offset);
    }

    public static function deleteUser($user_id): bool
    {
        $rowCount = User::delete($user_id);
        if ($rowCount > 0) {
            return true;
        }
        return false;
    }
}