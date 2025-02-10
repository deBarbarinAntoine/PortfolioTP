<?php

if (!isset($_POST['token']) || !isset($_POST['current_password']) || !isset($_POST['newPassword'])) {
    $error_message = urlencode("Invalid request.");
    header("Location: /login?error_message=" . $error_message);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


use App\Controllers\UserController;
use App\Controllers\PasswordResetController;

$previousPage = $_SESSION['previous_page'] ?? '/login'; // previous_page = edit_profile

$userController = new UserController();
$resetPasswordController = new PasswordResetController();
$reset_password_token = htmlspecialchars($_POST['token']);
$oldPassword = $_POST['current_password'];
$newPassword = $_POST['newPassword'];

$TokenMail = $resetPasswordController->findTokenMail($reset_password_token);
$user_id = $userController->getUserIdFromMail($TokenMail);


if ((!empty($oldPassword) && !empty($newPassword)) && $user_id != -1 ) {
    $isOldPasswordValid = $userController->checkOldPassword($oldPassword, $TokenMail, $user_id);
    if (!$isOldPasswordValid) {
        $error_message = urlencode("Invalid current password.");
        header("Location: /reset?error_message=" . $error_message);
        exit;
    }

    $isNewValid = $userController->validatePassword($user_id, $newPassword);
    if (is_string($isNewValid)) {
        $error_message = urlencode($isNewValid);
        header("Location: /reset?error_message=" . $error_message);
        exit;
    }

    $updateUser = $userController->updateUserPassword($user_id, $newPassword);
    if (!$updateUser) {
        $error_message = urlencode("Unable to update password.");
        header("Location: /reset?error_message=" . $error_message);
        exit;
    }

    $success_message = urlencode("Password updated successfully.");
    header("Location: /reset?success_message=" . $success_message);
    exit;
}
?>



