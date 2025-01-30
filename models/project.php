<?php

namespace models;

require_once __DIR__ . '/db.php';

class Project {
    public static function getPublicProjects() {
        global $conn; // Use the database connection

        $query = "SELECT * FROM projects WHERE private = 'public' ORDER BY created_at DESC";
        $result = $conn->query($query);

        return $result->fetch_all(MYSQLI_ASSOC); // Return projects as an associative array
    }
}