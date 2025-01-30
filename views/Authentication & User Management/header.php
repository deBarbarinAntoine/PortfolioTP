<?php
session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - ProjetB2</title>
    <link rel="stylesheet" href="headerStyles.css"> <!-- Assurez-vous d’avoir un fichier CSS -->
</head>
<body>

<!-- Navigation -->
<nav>
    <div class="logo">ProjetB2</div>
    <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="projects.php">Projets</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="profile.php">Mon Profil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="login.php">Connexion</a></li>
            <li><a href="register.php">Inscription</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Welcome Section -->
<header>
    <h1>Bienvenue sur ProjetB2</h1>
    <p>Une plateforme pour gérer vos compétences et projets.</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn">S'inscrire</a>
    <?php endif; ?>
</header>

