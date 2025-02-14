<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$previousPage = $_SESSION['previous_page'] ?? '/login'; // previous page should be edit_profile.php
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
use App\Models\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Controllers\UserController;

$userController = new UserController();

if (!$userController->checkAnyUserHaveEmail($userEmail)) {
    $success_message = urlencode("If email exist in our base, a mail will be sent with the link for password reset.");
    header("Location: " . htmlspecialchars($previousPage) . "?success_message=" . $success_message);
    exit;
}

$passwordResetController = new PasswordResetController();

// Get the environment variables
$mailSender = $_ENV['MAIL_SENDER'] || 'PortfolioTP <no-reply@PortfolioTP.com>';
$mailAddress= $_ENV['MAIL_ADDRESS'] || 'mail@example.com';
$mailUsername = $_ENV['MAIL_USERNAME'] || 'mail@example.com';
$mailPassword = $_ENV['MAIL_PASSWORD'] || 'password';
$mailHost = $_ENV['MAIL_HOST'] || 'smtp.mail.io';
$mailPort = $_ENV['MAIL_PORT'] || '587';
$mailEncryption = $_ENV['MAIL_ENCRYPTION'] || 'PHPMailer::ENCRYPTION_STARTTLS';

// Ensure that the necessary values are set
if (!$mailUsername || !$mailPassword || !$mailHost || !$mailPort || !$mailEncryption) {
    // TO DO log it 'Missing necessary Mailtrap credentials. Please check your .env file.';
    $error_message = urlencode("An error occurred. Please try again later.");
    header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
    exit;
}

function getServerURL(): string
{
    $server_name = $_SERVER['SERVER_NAME'];

    if (!in_array($_SERVER['SERVER_PORT'], [80, 443])) {
        $port = ":$_SERVER[SERVER_PORT]";
    } else {
        $port = '';
    }

    if (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) {
        $scheme = 'https';
    } else {
        $scheme = 'http';
    }
    return $scheme.'://'.$server_name.$port;
}

try {
    // Generate a password reset token
    $resetToken = $passwordResetController->generateResetToken($userEmail);

    if ($resetToken === null) {
        $error_message = urlencode("The reset token could not be generated. Please try again.");
        header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
        exit;
    }
    $resetLink = getServerURL() . "/reset?token=" . urlencode($resetToken);

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
    $mail->setFrom($mailAddress, $mailSender);
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

    // LOGGING
    Logger::log("Failed to send the password reset email: " . $e->getMessage(), __FILE__);

    $error_message = urlencode("An error occurred. Please try again later.");
    header("Location: " . htmlspecialchars($previousPage) . "?error_message=" . $error_message);
    exit;
}