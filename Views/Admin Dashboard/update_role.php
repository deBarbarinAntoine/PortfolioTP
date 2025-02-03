<?php

use App\Controllers\UserController;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed.");
}

// Validate input
if (!isset($_POST['user_id'], $_POST['new_role'])) {
    die("Invalid input.");
}

$user_id = (int) $_POST['user_id'];
$new_role = $_POST['new_role'];
$allowed_roles = ['admin', 'user'];

if (!in_array($new_role, $allowed_roles)) {
    die("Invalid role selected.");
}

$roleController = new UserController();

try {
    $success = $roleController->updateRole($user_id, $new_role);
    if ($success > 0) {
        $_SESSION['success_message'] = 'Role updated successfully! (Affected Rows: $success)';
    } else {
        $_SESSION['error_message'] = 'Failed to update role. (Affected Rows: $success) Please try again.';
    }
} catch (DateMalformedStringException $e) {
    echo $e->getMessage();
}
?>
