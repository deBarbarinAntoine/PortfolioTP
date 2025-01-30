<?php

include 'header.php';

require_once __DIR__ . '/../../controllers/ProjectController.php';

$projectController = new ProjectController();
$projects = $projectController->getPublicProjects();
?>

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
include 'footer.php';
?>