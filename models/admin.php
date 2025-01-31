<?php

namespace models;

use DateMalformedStringException;
use PDOException;

class admin
{
    public static function getAdminDashboard()
    {
        try {
            $users_count = user::getCountAll();
            $users_count_last24hours = user::getCountLastUsers();
            $latest_users = user::get5LastUsers();
            $skills_count = skill::getCountAll();
            $skills = skill::getAllSkills();
            $projects_count = project::getCountAll();
            $projects_count_last24hours = project::getCountLastProject();

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
            die("Error fetching data: " . $e->getMessage());
        }
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function get_admin_users(string $search, int $offset): array
    {
        return user::getAllUsers($search, $offset);
    }

    public static function deleteUser($user_id): bool
    {
        $rowCount = user::delete($user_id);
        if ($rowCount > 0) {
            return true;
        }
        return false;
    }
}