<?php
$_SESSION = [];      // clear all session variables
session_unset();     // Unset / destroy all session variables
session_destroy();   // Destroy the session

// Also delete the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to the login page with a message
$message = $_GET['message'] ?? 'You have been logged out successfully';
header("Location: /login?message=" . urlencode($message));
exit();
?>