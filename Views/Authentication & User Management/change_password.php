<?php

if (!isset($_GET['token']) || !isset($_GET['current_password']) || !isset($_GET['newPassword'])) {
    $error_message = urlencode("Invalid request.");
    header("Location: login.php?error_message=" . $error_message);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


use App\Controllers\UserController;
use App\Controllers\PasswordResetController;

$previousPage = $_SESSION['previous_page'] ?? 'login.php'; // previous_page = edit_profile

$userController = new UserController();
$resetPasswordController = new PasswordResetController();

$user = null;
$error_message = null;
$reset_password_token = htmlspecialchars($_GET['token']);

$TokenMail = $resetPasswordController->findTokenMail($reset_password_token);
$user_id = $userController->getUserIdFromMail($TokenMail);


if ((!empty($oldPassword) && !empty($newPassword)) && $user_id != -1 ) {
    $isOldPasswordValid = $userController->checkOldPassword($oldPassword, $TokenMail, $user_id);

    try {
        $isOldPasswordValid = $userController->validatePassword($user_id, $newPassword);
    } catch (DateMalformedStringException $e) {

    }
    if (!$isOldPasswordValid) {
        $error_message = urlencode("Invalid current password.");
        header("Location: reset_password.php?error_message=" . $error_message);
    }
    $newPasswordHash = $userController->hashPassword($user_id, $newPassword);
}
?>



