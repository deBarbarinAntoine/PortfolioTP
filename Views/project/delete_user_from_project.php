<?php

use App\Controllers\User_ProjectController;


if (!isset($projectId)) {
    $uri = $_SERVER['REQUEST_URI']; // Example: "/project/3/something"
    $segments = explode('/', trim($uri, '/'));

    if (isset($segments[1])) { // Ensure the second segment exists
        $projectId = $segments[1]; // Get the second segment
    }
}

$errors = [];

include "Views/templates/header.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['csrf_token'])) {
    $error_message = "Please Log In First";
    header('Location: '.$_POST['previousPage'].'?error_message=' . urlencode($error_message));
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        $errors[] = "Sorry, you are not authorized to post to this page";
    }

    if (!isset($_POST['project_id']) || $projectId !== $_POST['project_id']) {
        $errors[] = "Sorry, which project ?";
    }

    // Sanitize $_POST['project_id'] and $_POST['user_id']
    $projectIdFromPost = isset($_POST['project_id']) ? filter_var($_POST['project_id'], FILTER_VALIDATE_INT) : null;

    // If invalid values are received, return an error or handle the issue
    if ($projectIdFromPost === false) {
        $errors[] = "Invalid project ID.";
    }

    if (!$errors) {
        $userProjectController = new User_ProjectController();
        if (isset($_POST['contributor_id'])) {
            $user_id = $_POST['contributor_id'];
        }else if (isset($_POST['viewer_id'])) {
            $user_id = $_POST['viewer_id'];
        }else {
            $error_message = "Sorry, no user was given , couldn't delete from project.";
            header('Location: '.$_POST['previousPage'].'?error_message=' . urlencode($error_message));
            exit;
        }
        $row = $userProjectController->deleteUserFromProject($user_id);
        if ($row <= 0) {
            $error_message = "Sorry, that user could not be deleted.";
            header('Location: '.$_POST['previousPage'].'?error_message=' . urlencode($error_message));
            exit;
        }
        $success_message = "User was successfully deleted from this project.";
        header('Location: '.$_POST['previousPage'].'?success_message=' . urlencode($success_message));
        exit;
    }
    header('Location: '.$_POST['previousPage'].'?' . http_build_query(['error_message' => $errors]));
    exit();
}