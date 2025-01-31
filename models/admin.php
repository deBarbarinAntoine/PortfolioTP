<?php

namespace models;

use DateMalformedStringException;
use PDOException;

class admin
{
    public static function getAdminDashboard()
    {

        // Fetch stats securely using prepared statements
        try {
            $users_count = user::getCountAll();
            $users_count_last24hours = user::getCountLastUsers();
            $latest_users = user::get5LastUsers();
            $skills_count = skill::getCountAll();
            $skills = skill::getAllSkills();

            $stmt = $conn->prepare("SELECT COUNT(*) FROM projects");
            $stmt->execute();
            $projects_count = $stmt->fetchColumn();

            $stmt = $conn->prepare("SELECT COUNT(*) FROM projects WHERE created_at >= NOW() - INTERVAL 24 HOUR");
            $stmt->execute();
            $projects_count_last24hours = $stmt->fetchColumn();
            






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
}