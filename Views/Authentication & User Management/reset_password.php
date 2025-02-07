<?php

if (!isset($_GET['token'])) {
    $error_message = urlencode("Invalid request.");
    header("Location: login.php?error_message=" . $error_message);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$previousPage = $_SESSION['previous_page'] ?? 'login.php'; // previous_page = edit_profile


use App\Controllers\PasswordResetController;

$reset_password_token = htmlspecialchars($_GET['token']);
$resetPasswordController = new PasswordResetController();

// Ensure the token is 64 characters long and only contains valid hexadecimal characters
if (strlen($reset_password_token) !== 64 || !ctype_xdigit($reset_password_token)) {
    // Invalid token format
    $error_message = urlencode("token timed out or invalid , please try again.");
    header("Location: login.php?error_message=" . $error_message);
    exit;
}

$isTokenValid = $resetPasswordController->isTokenValid($reset_password_token);

if (!$isTokenValid) {
    $error_message = urlencode("token timed out or invalid , please try again.");
    header("Location: ".$previousPage."?error_message=" . $error_message);
    exit;
}

$error_message = htmlspecialchars($_GET['error_message']);
?>


<h2>Edit Password</h2>
<?php if ($error_message): ?>
    <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
<?php endif; ?>
<form method="POST" action="change_password.php">
    <label for="current_password">Password:</label>
    <input type="password" id="current_password" name="current_password" required>

    <label for="newPassword">New Password:</label>
    <input type="password" id="newPassword" name="newPassword">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($reset_password_token); ?>">
</form>
