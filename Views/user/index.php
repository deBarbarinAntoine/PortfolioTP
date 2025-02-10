<?php

use App\Controllers\ProjectController;
use App\Models\Level;
use App\Models\Logger;

include "Views/templates/header.php";

$projectController = new ProjectController();
try {
    $projects = $projectController->getPublicProjects();
} catch (DateMalformedStringException $e) {
    // Debug
    Logger::log($e->getMessage(), __FILE__, Level::DEBUG);
}
$error = '';
if (isset($_GET['error_message'])) {
    $error = $_GET['error_message'];
}
?>

    <!-- Welcome Section -->
    <header>
        <h1>Bienvenue sur ProjetB2</h1>
        <p>Une plateforme pour gérer vos compétences et projets.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/register" class="btn">S'inscrire</a>
        <?php endif; ?>
    </header>
<?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
<!-- Public Projects Section -->
<section class="projects">
    <h2>Projets Publics</h2>
    <div class="project-list">
        <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <img src="../public/uploads/<?= htmlspecialchars($project['image']) ?>" alt="Image du projet">
                    <h3><?= htmlspecialchars($project['title']) ?></h3>
                    <p><?= htmlspecialchars(substr($project['details'], 0, 100)) ?>...</p>
                    <a href="<?= htmlspecialchars($project['external_link']) ?>" target="_blank">Voir le projet</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun projet disponible.</p>
        <?php endif; ?>
    </div>
</section>

<?php
include 'Views/templates/footer.php';
?>