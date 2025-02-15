<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$session_timeout = 900; // 15 minutes
// Check if the last activity timestamp is set
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $session_timeout) {
    // If the session is expired, logout user
    // Redirect to login page
    header('Location: /logout?message=Session expired please login again');
    exit(); // Make sure to exit to stop further execution
}


if (isset($_SESSION['LAST_ACTIVITY'])) {
    if ((time() - $_SESSION['LAST_ACTIVITY']) > $session_timeout) {
        header('Location: /logout?message=' . urlencode("Session expired. Please log in again."));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - ProjetB2</title>
    <link rel="icon" href="/favicon" type="image/x-icon">
    <link rel="stylesheet" href="../style.css"> <!-- Assurez-vous d’avoir compilé le fichier SCSS -->
</head>
<body>

<!-- Navigation -->
<nav>
    <div class="logo">ProjetB2</div>
    <ul>
        <li><a href="/">Accueil</a></li>
        <li><a href="/projects">Projets</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li><a href="/admin">Admin Dashboard</a></li>
            <?php endif; ?>
            <li><a href="/profile">Mon Profil</a></li>
            <li><a href="/logout">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="/login">Connexion</a></li>
            <li><a href="/register">Inscription</a></li>
        <?php endif; ?>
    </ul>
</nav>



