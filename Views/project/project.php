<?php
// projects with details.
include "Views/templates/header.php";

use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;
use App\Controllers\UserController;

if (isset($GLOBALS['id'])) {
    $projectID = $GLOBALS['id'];
}

if (isset($_GET['error_message'])) {
    $error = htmlspecialchars($_GET['error_message']);
}

if (isset($_GET['success_message'])) {
    $success = htmlspecialchars($_GET['success_message']);
}

if (!isset($projectID)) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Extracts only the path, ignoring query parameters
    $segments = explode('/', trim($uri, '/'));
    $projectID = end($segments); // Get the last segment
}



if (!isset($GLOBALS['id']) && !isset($projectID)) {
    $errorMessage = 'Please select a valid project, selected id =  ' . $projectID ;
    header("Location: /?error_message=$errorMessage");
    exit;
}

$projectId = $projectID ?? "";
$isOwner = false;
$projectController = new ProjectController();
$user_projectController = new User_ProjectController();
$userController = new UserController();
try {
    $projectData = $projectController->getProject($projectId);
    $projectContributors = $user_projectController->getContributors($projectId);
    foreach ($projectContributors as $key => $projectContributor) {
        $projectContributorID = $projectContributor['user_id'];
        $userContributor = $userController->getUser($projectContributorID);
        $projectContributors[$key]['user'] = $userContributor;
    }


    $projectViewers= $user_projectController->getViewers($projectId);
    foreach ($projectViewers as $key => $projectViewer) {
        $projectViewersID = $projectViewer['user_id'];
        $userViewer = $userController->getUser($projectViewersID);
        $projectViewers[$key]['user'] = $userViewer;
    }

    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $isOwner = $user_projectController->getIsOwner($projectId, $userId);
    }
} catch (Exception $e) {
    $errorMessage = "Failed to find the project ".$e->getMessage();
    exit;
}

?>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <div class="project-container">
        <h1 class="project-title"><?php echo htmlspecialchars($projectData->getTitle()); ?></h1>

        <p><strong> Visibility :</strong> <?php echo nl2br(htmlspecialchars($projectData->getVisibilityStr() ?? "No Visibility available.")); ?></p>

        <p class="project-description">
            <strong>Title : </strong><?php echo nl2br(htmlspecialchars( $projectData->getDescription() ?? "No description available.")); ?>
        </p>

        <?php if (!empty($projectData->getExternalLink())) : ?>
            <p><strong>External Link:</strong> <a href="<?php echo htmlspecialchars($projectData->getExternalLink()); ?>" target="_blank">
                    <?php echo htmlspecialchars($projectData->getExternalLink()); ?>
                </a></p>
        <?php endif; ?>

        <p><strong>Created At:</strong>
            <?php echo htmlspecialchars($projectData->getCreatedAt()?->format('Y-m-d H:i:s') ?? "Unknown"); ?>
        </p>

        <p><strong>Last Updated:</strong>
            <?php echo htmlspecialchars($projectData->getUpdatedAt()?->format('Y-m-d H:i:s') ?? "Unknown"); ?>
        </p>

        <h3>Project Images:</h3>
        <div class="project-images">
            <?php if (!empty($projectData->getImages())) : ?>
                <?php foreach ($projectData->getImages() as $image) : ?>
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Project Image">
                <?php endforeach; ?>
            <?php else : ?>
                <p>No images available for this project.</p>
            <?php endif; ?>
        </div>

        <h3>Project Contributor:</h3>
        <div>
            <?php if (!empty($projectContributors)) : ?>
                <?php foreach ($projectContributors as $projectContributor) : ?>
                    <hr>
                    <div>
                        <!-- Display User Details -->
                        <p>Username: <?= htmlspecialchars($projectContributor['user']->getUsername()) ?></p>
                        <p>Email: <?= htmlspecialchars($projectContributor['user']->getEmail()) ?></p>
                        <img src="<?= htmlspecialchars($projectContributor['user']->getAvatar()) ?>" alt="User Avatar" width="50">

                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No Project Contributor for this project.</p>
            <?php endif; ?>
        </div>

        <h3>Project Viewer:</h3>
        <div>
            <?php if (!empty($projectViewers)) : ?>
                <?php foreach ($projectViewers as $projectViewer) : ?>
                    <div>
                        <!-- Display User Details -->
                        <p>Username: <?= htmlspecialchars($projectViewer['user']->getUsername()) ?></p>
                        <p>Email: <?= htmlspecialchars($projectViewer['user']->getEmail()) ?></p>
                        <img src="<?= htmlspecialchars($projectViewer['user']->getAvatar()) ?>" alt="User Avatar" width="50">

                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No Project Viewer for this project.</p>
            <?php endif; ?>
        </div>

        <?php if ($isOwner): ?>
            <!-- Owner Form to Add User to Project -->
            <h3>Add User to Project</h3>
            <form action="<?php echo '/project/' . $projectID . '/add'; ?>" method="POST">
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


<?php include "Views/templates/footer.php"; ?>