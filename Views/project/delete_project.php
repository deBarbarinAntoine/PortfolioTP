<?php
// POST /project/$ParamID/delete
// $ParamID & Check csrf_token – Handles project deletion

use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;

$projectId = isset($ParamID) ? (int)$ParamID : '';  // Sanitize the $ParamID
$errors = [];


include "../user/header.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['csrf_token'])) {
    $error_message = "Please Log In First";
    header('Location: login.php?error_message=' . urlencode($error_message));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        $errors[] = "Sorry, you are not authorized to post to this page";
    }

    if (!isset($_POST['project_id']) || $projectId !== $_POST['project_id']) {
        $errors[] = "Sorry, which project ?";
    }

    if (!isset($_POST['user_id']) || $_POST['user_id'] !== $_SESSION['user_id']) {
        $errors[] = "Sorry, who are you ?";
    }

    // Sanitize $_POST['project_id'] and $_POST['user_id']
    $projectIdFromPost = isset($_POST['project_id']) ? filter_var($_POST['project_id'], FILTER_VALIDATE_INT) : null;
    $userIdFromPost = isset($_POST['user_id']) ? filter_var($_POST['user_id'], FILTER_VALIDATE_INT) : null;

    // If invalid values are received, return an error or handle the issue
    if ($projectIdFromPost === false) {
        $errors[] = "Invalid project ID.";
    }

    if ($userIdFromPost === false) {
        $errors[] = "Invalid user ID.";
    }

    $user_projectController = new User_ProjectController();
    $userId = $_SESSION['user_id'];

    if (!$user_projectController->isUserAllowedToDelete($projectId, $userId)) {
        $errors[] = "Sorry, you are not authorized to delete this project";
    }

    if (empty($errors)) {
        $projectController = new ProjectController();
        $rows = $projectController->deleteProject($projectId);
        if ($rows <= 0) {
            $errors[] = "Sorry, there was a problem with the delete operation";
            header('Location: project.php?id=' . $projectId . '&error_message=' . urlencode(implode(', ', $errors)));

        }
        header('Location: my_projects.php?message=' . urlencode($rows . ' project(s) have been deleted'));
    }

}