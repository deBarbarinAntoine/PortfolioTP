<?php
// POST /project/$GLOBALS['id']/delete
// $GLOBALS['id'] & Check csrf_token – Handles project deletion

use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;


if (!isset($projectId)) {
    $projectId = $GLOBALS['id'];
}

$errors = [];

include "Views/templates/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        $errors[] = "Sorry, you are not authorized to post to this page";
    }

    if (!isset($_POST['project_id']) || $projectId !== $_POST['project_id']) {
        $errors[] = "Sorry, which project ?";
    }

    if (!isset($_POST['user_id']) || (int) $_POST['user_id'] !== $_SESSION['user_id']) {
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
        $userProjectController = new User_ProjectController();
        $rows = $projectController->deleteProject($projectId);
        $rows1 = $userProjectController->deleteProject($projectId);
        if ($rows <= 0) {
            $errors[] = "Sorry, there was a problem with the delete operation";
            header('Location: /project/' . $projectId . '&error_message=' . urlencode(implode(', ', $errors)));
            exit;
        }
        if ($rows1<=0){
            $errors[] = "Sorry, there was a problem with the delete operation for your roles, please contact support to debug with data : projectId = " . $projectId;
            header('Location: /projects' . '&error_message=' . urlencode(implode(', ', $errors)));
            exit;
        }

        header('Location: /projects?message=' . urlencode($rows . ' project(s) have been deleted successfully with ' . $rows1 . ' project(s) role(s)'));
        exit;
    }

}