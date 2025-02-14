<?php

namespace App\Models;

use DateMalformedStringException;
use PDOException;

/**
 * The Admin class provides methods for handling administrative functionalities,
 * such as managing user data, fetching dashboard statistics, and handling skills and users.
 * It interacts with related models like User, Skill, and Project.
 */
class Admin
{
    /**
     * Retrieves data for the admin dashboard, including user, skill, and project statistics.
     *
     * @param string $search Search term for filtering skills.
     * @param int $offset Pagination offset for skills.
     * @return array An associative array containing user, skill, and project statistics.
     * @throws PDOException If an error occurs during data retrieval.
     */
    public static function getAdminDashboard(string $search, int $offset): array
    {
        try {
            // Fetch the total count of users in the system.
            $users_count = User::getCountAll();

            // Fetch the count of users registered in the last 24 hours.
            $users_count_last24hours = User::getCountLastUsers();

            // Fetch details of the 5 latest registered users.
            $latest_users = User::get5LastUsers();

            // Fetch the total count of skills available in the system.
            $skills_count = Skill::getCountAll();

            // Fetch skills with optional search and pagination.
            $skills = Skill::getAllSkills($search, $offset);

            // Fetch the total count of projects.
            $projects_count = Project::getCountAll();

            // Fetch the count of projects created in the last 24 hours.
            $projects_count_last24hours = Project::getCountLastProject();

            // Return the aggregated dashboard data as an associative array.
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

            // Log the exception details for debugging and tracing.
            Logger::log("Error fetching data: " . $e->getMessage(), __METHOD__);

            // Terminate the script with a user-friendly error message.
            die("Error fetching data: " . $e->getMessage());
        }
    }

    /**
     * Retrieves a paginated list of all users that match the search criteria.
     *
     * @param string $search Search term for filtering users.
     * @param int $offset Pagination offset for the user list.
     * @return array A list of users matching the search criteria.
     * @throws DateMalformedStringException If an invalid date string is encountered internally.
     */
    public static function get_admin_users(string $search, int $offset): array
    {
        return User::getAllUsers($search, $offset);
    }

    /**
     * Deletes a user by their ID.
     *
     * @param int $user_id The ID of the user to be deleted.
     * @return bool True if the user was successfully deleted, false otherwise.
     */
    public static function deleteUser(int $user_id): bool
    {
        // Attempt to delete the user and fetch the number of affected rows.
        $rowCount = User::delete($user_id);

        // Return true if at least one row was affected, indicating successful deletion.
        return $rowCount > 0;
    }

    /**
     * Retrieves a list of skills with optional filtering and pagination.
     *
     * @param mixed $search Search term for filtering skills.
     * @param mixed $offset Pagination offset for skills.
     * @return array A list of skills matching the search criteria.
     * @throws DateMalformedStringException
     */
    public static function get_admin_skills(mixed $search, mixed $offset): array
    {
        return Skill::getAllSkills($search, $offset);
    }
}