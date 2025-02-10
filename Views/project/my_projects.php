<?php

use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

$message = "";
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

$user_id = (int) $_SESSION['user_id'];
$_SESSION['previousPage'] = "/projects";
$errorMessage = [];

$projectController = new ProjectController();
$userProjectsController = new User_ProjectController();

$projectList = $userProjectsController->getUserProject($user_id);
$projectsByRole = [];

foreach ($projectList as $projectData) {
    $role = $projectData['role'];
    $projectId = $projectData['id'];
    try {
        $projectContent = $projectController->getProject($projectId);
        $projectsByRole[$role][] = $projectContent;
    } catch (Exception $e) {
        $errorMessage[] = "Failed to get project: " . $e->getMessage();
    }
}
?>

<?php if (!empty($errorMessage)): ?>
    <div class="error-messages">
        <?php foreach ($errorMessage as $error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($message)): ?>
    <div class="messages">
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    </div>
<?php endif; ?>

    <h1>User Projects</h1>

<?php if (empty($projectsByRole)): ?>
    <p>No projects found for this user.</p>
    <!-- Add Button to add a new project -->
    <a href="/add_project.php" class="btn btn-primary">Add New Project</a>
<?php else: ?>
    <div class="project-list">
        <?php foreach ($projectsByRole as $role => $projects): ?>
            <h2><?= ucfirst($role) ?> Projects</h2>

            <?php if (empty($projects)): ?>
                <p>No projects for this role.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($projects as $project): ?>
                        <li>
                            <strong>Project Name:</strong>
                            <a href="/project/<?= htmlspecialchars($project['id']) ?>?name=<?= urlencode($project['title']) ?>">
                                <?= htmlspecialchars($project['title']) ?>
                            </a> <br>
                            <strong>Description:</strong> <?= htmlspecialchars($project['description']) ?> <br>
                            <strong>External Link:</strong> <a href="<?= htmlspecialchars($project['external_link']) ?>" target="_blank">Visit</a> <br>
                            <strong>Visibility:</strong> <?= htmlspecialchars($project['visibility']) ?> <br>
                            <strong>Created At:</strong> <?= htmlspecialchars($project['created_at']) ?> <br>
                            <strong>Updated At:</strong> <?= htmlspecialchars($project['updated_at']) ?> <br>

                            <?php if (!empty($project['images'])): ?>
                                <strong>Images:</strong><br>
                                <?php foreach ($project['images'] as $image): ?>
                                    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="Project Image" style="max-width: 200px; display: block; margin-top: 5px;">
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Edit and Delete Buttons -->
                            <br>
                            <a href="edit_project.php?id=<?= $project['id'] ?>" class="btn btn-secondary">Edit Project</a>
                            <a href="delete_project.php?id=<?= $project['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this project?');">Delete Project</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Add Button for Adding New Project -->
    <a href="add_project.php" class="btn btn-primary">Add New Project</a>
<?php endif; ?>