<?php

include "Views/templates/header.php";

// Check if the user is an admin
use App\Controllers\UserController;

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: /admin?error_message=CSRF validation failed");
    exit();
}

// Ensure data is received
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'], $_POST['new_role'])) {
    $userId = intval($_POST['user_id']);
    $newRole = $_POST['new_role'];

    // Validate role input
    if (!in_array($newRole, ['admin', 'user'])) {
        header("Location: /admin?error_message=invalid_role");
        exit();
    }

    $userController = new UserController();
    try {
        $success = $userController->updateRole($userId, $newRole);
    } catch (DateMalformedStringException $e) {
        header("Location: /admin?error_message=error while updating role : " . $e->getMessage());
        exit();
    }

    if ($success<=0) {
        header("Location: /admin?error_message=error while updating role");
        exit();
    }
    // Redirect back to the users list
    header("Location: /admin?success_message=role correctly changed");
    exit();
}
