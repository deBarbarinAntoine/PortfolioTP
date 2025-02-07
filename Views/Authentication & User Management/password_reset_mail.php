<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$previousPage = $_SESSION['previous_page'] ?? 'login.php'; // previous page should be edit_profile.php
$previousPage = filter_var($previousPage, FILTER_SANITIZE_URL);

if (!isset($_GET['email'])) {
    $error_message = urlencode("Invalid request.");
    header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
    exit;
}

$userEmail = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);

if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
    $error_message = urlencode("Invalid email format.");
    header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
    exit;
}

// Import necessary classes
use App\Controllers\PasswordResetController;
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Controllers\UserController;

$userController = new UserController();

if (!$userController->checkAnyUserHaveEmail($userEmail)) {
    $success_message = urlencode("If email exist in our base, a mail will be sent with the link for password reset.");
    header("Location: " . htmlspecialchars($previousPage) . "?success_message=" . $success_message);
    exit;
}

require 'vendor/autoload.php'; // Include PHPMailer's autoload

$passwordResetController = new PasswordResetController();

// Get the environment variables
$mailUsername = $_ENV['MAIL_USERNAME'];
$mailPassword = $_ENV['MAIL_PASSWORD'];
$mailHost = $_ENV['MAIL_HOST'];
$mailPort = $_ENV['MAIL_PORT'];
$mailEncryption = $_ENV['MAIL_ENCRYPTION'];

// Ensure that the necessary values are set
if (!$mailUsername || !$mailPassword || !$mailHost || !$mailPort || !$mailEncryption) {
    // TO DO log it 'Missing necessary Mailtrap credentials. Please check your .env file.';
    $error_message = urlencode("An error occurred. Please try again later.");
    header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
    exit;
}

try {
    // Generate a password reset token
    $resetToken = $passwordResetController->generateResetToken($userEmail);

    if ($resetToken === null) {
        $error_message = urlencode("The reset token could not be generated. Please try again.");
        header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
        exit;
    }

    $port = $_ENV['DB_PORT'] ?? '8000'; // Default port for local testing
    $resetLink = "http://localhost:" . htmlspecialchars($port) . "/reset_password.php?token=" . urlencode($resetToken);

    // Send email using mail() , replace $mail->send() lower to mail(); keep in mind that mail(); use your php.ini config
    //    $subject = "Password Reset Request";
    //    $message = "Hello,\n\nClick the link below to reset your password:\n$resetLink\n\nIf you did not request this, please ignore this email.";
    //    $headers = [
    //        "From: no-reply@PortofolioTp.com",
    //        "Content-Type: text/plain; charset=UTF-8"
    //    ];


    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    // Configure PHPMailer to use SMTP
    $mail->isSMTP();
    $mail->Host = $mailHost;  // Mail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = $mailUsername;  // Mailtrap username
    $mail->Password = $mailPassword;  // Mailtrap password
    $mail->SMTPSecure = $mailEncryption; // TLS encryption
    $mail->Port = $mailPort; // Port for TLS

    // Sender and recipient details
    $mail->setFrom('no-reply@PortofolioTp.com', 'No Reply');
    $mail->addAddress($userEmail);  // Add recipient's email

    // Set email format to HTML
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';
    $mail->Body = "Hello,<br><br>Click the link below to reset your password:<br><a href=\"$resetLink\">Reset Password</a><br><br>If you did not request this, please ignore this email.";

    // Send email
    if ($mail->send()) {
        $success_message = urlencode("If email exist in our base, a mail will be sent with the link for password reset.");
        header("Location: " . htmlspecialchars($previousPage) . "?success_message=" . $success_message);
    } else {
        $error_message = urlencode("Failed to send the password reset email.");
        header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
    }
    exit;
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage()); // Log error for debugging
    $error_message = urlencode("An error occurred. Please try again later.");
    header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
    exit;
}