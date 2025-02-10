<?php
// projects with details.
include "../user/header.php";

use App\Controllers\ProjectController;

if (!isset($_GET['name']) || !isset($_GET['id'])) {
    $errorMessage = 'Please select a valid project';
    header("Location: index.php?error_message=$errorMessage");
    exit;
}

$projectId= htmlspecialchars($_GET['id']);

$projectController = new ProjectController();
try {
    $projectData = $projectController->getProject($projectId);
} catch (Exception $e) {
    $errorMessage = "Failed to find the project ".$e->getMessage();
    exit;
}

?>

<div class="project-container">
    <h1 class="project-title"><?php echo htmlspecialchars($projectData->getTitle()); ?></h1>

    <p class="project-description">
        <?php echo nl2br(htmlspecialchars($projectData->getDescription() ?? "No description available.")); ?>
    </p>

    <?php if (!empty($projectData->getExternalLink())) : ?>
        <p><strong>External Link:</strong> <a href="<?php echo htmlspecialchars($projectData->getExternalLink()); ?>" target="_blank">
                <?php echo htmlspecialchars($projectData->getExternalLink()); ?>
            </a></p>
    <?php endif; ?>

    <p><strong>Created At:</strong> <?php echo htmlspecialchars($projectData->getCreatedAt() ?? "Unknown"); ?></p>
    <p><strong>Last Updated:</strong> <?php echo htmlspecialchars($projectData->getUpdatedAt() ?? "Unknown"); ?></p>

    <h3>Project Images:</h3>
    <div class="project-images">
        <?php if (!empty($projectData->getImages())) : ?>
            <?php foreach ($projectData->getImages() as $image) : ?>
                <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Project Image">
            <?php endforeach; ?>
        <?php else : ?>
            <p>No images available for this project.</p>
        <?php endif; ?>
    </div>

</div>

<?php include "../user/footer.php"; ?>