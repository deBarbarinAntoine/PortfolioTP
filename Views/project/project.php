<?php
// projects with details.
include "Views/templates/header.php";

use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;

if (!isset($_GET['name']) || !isset($_GET['id'])) {
    $errorMessage = 'Please select a valid project';
    header("Location: index.php?error_message=$errorMessage");
    exit;
}

$projectId= htmlspecialchars($_GET['id']);
$isOwner = false;
$projectController = new ProjectController();
$user_projectController = new User_ProjectController();
try {
    $projectData = $projectController->getProject($projectId);
    if (!isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $isOwner = $user_projectController->getIsOwner($projectId, $userId);
    }
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

        <?php if ($isOwner): ?>
            <!-- Owner Form to Add User to Project -->
            <h3>Add User to Project</h3>
            <form action="add_user_to_project.php" method="POST">
                <div>
                    <label for="email">User Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="contributor">Contributor</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
                <button type="submit">Add User</button>
            </form>
        <?php endif; ?>

    </div>


<?php include "../templates/footer.php"; ?>