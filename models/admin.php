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

            $stmt = $conn->prepare("SELECT COUNT(*) FROM skills");
            $stmt->execute();
            $skills_count = $stmt->fetchColumn();

            $stmt = $conn->prepare("SELECT COUNT(*) FROM projects");
            $stmt->execute();
            $projects_count = $stmt->fetchColumn();

            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE created_at >= NOW() - INTERVAL 24 HOUR");
            $stmt->execute();
            $users_count_last24hours = $stmt->fetchColumn();
            user::getLastUsers();

            $stmt = $conn->prepare("SELECT COUNT(*) FROM projects WHERE created_at >= NOW() - INTERVAL 24 HOUR");
            $stmt->execute();
            $projects_count_last24hours = $stmt->fetchColumn();

            // Fetch latest users
            $stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
            $stmt->execute();
            $latest_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch skills
            $stmt = $conn->prepare("SELECT id, name, description FROM skills ORDER BY name");
            $stmt->execute();
            $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

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