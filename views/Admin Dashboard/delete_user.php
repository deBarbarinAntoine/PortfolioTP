<?php

use controllers\AdminController;

session_start();
require_once __DIR__ . '/../../controllers/AdminController.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }

    $user_id = intval($_POST['user_id']);
    $adminController = new AdminController();

    if ($adminController->deleteUser($user_id)) {
        header("Location: admin_users.php?msg=User Deleted Successfully");
    } else {
        header("Location: admin_users.php?msg=User Not Deleted");
    }
}
?>
