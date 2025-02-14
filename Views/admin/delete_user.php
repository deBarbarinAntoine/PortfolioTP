<?php

use App\Controllers\AdminController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }

    $user_id = intval($_POST['user_id']);
    $adminController = new AdminController();

    if ($adminController->deleteUser($user_id)) {
        header("Location: /admin/users?msg=User Deleted Successfully");
    } else {
        header("Location: /admin/users?msg=User Not Deleted");
    }
}
?>
