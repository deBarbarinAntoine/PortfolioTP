<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$session_timeout = 900; // 15 minutes
// Check if the last activity timestamp is set
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $session_timeout) {
    // If the session is expired, logout user
    // Redirect to login page
    header('Location: logout.php?message=Session expired please login again');
    exit(); // Make sure to exit to stop further execution
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - ProjetB2</title>
    <link rel="stylesheet" href="../style.css"> <!-- Assurez-vous d’avoir compilé le fichier SCSS -->
</head>
<body>

<!-- Navigation -->
<nav>
    <div class="logo">ProjetB2</div>
    <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="../Project%20Management/projects.php">Projets</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="../Admin%20Dashboard/admin_dashboard.php">Admin Dashboard</a></li>
            <?php endif; ?>
            <li><a href="profile.php">Mon Profil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="login.php">Connexion</a></li>
            <li><a href="register.php">Inscription</a></li>
        <?php endif; ?>
    </ul>
</nav>



