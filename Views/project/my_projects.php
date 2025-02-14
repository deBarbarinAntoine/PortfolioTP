<?php

use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;

include "Views/templates/header.php";

$message = "";
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

$user_id = (int) $_SESSION['user_id'];
$_SESSION['previousPage'] = "/projects";
$errorMessage = [];
if (isset($_GET['errorMessage'])) {
    $errorMessage = $_GET['errorMessage'];
}

$projectController = new ProjectController();
$userProjectsController = new User_ProjectController();

$projectsByRole = $userProjectsController->getUserProject($user_id);

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

    <div class="project-list">
        <?php foreach ($projectsByRole as $role => $projects): ?>
            <h2><?= ucfirst($role) ?> Projects</h2>

            <?php if (empty($projects)): ?>
                <p>No projects for this role.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($projects as $project): ; ?>
                        <li>
                            <strong>Project Name:</strong>
                            <a href="/project/<?= htmlspecialchars($project['project_id']) ?>">
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

                            <!-- Only display Edit and Delete buttons for projects where the role is 'owner' -->
                            <?php if ($role == 'owner'): ?>
                                <!-- Edit and Delete Buttons -->
                                <br>
                                <a href="/project/<?= $project['id'] ?>/update" class="btn btn-secondary">Edit Project</a>

                                <form action="/project/<?= $project['id'] ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete Project</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Add Button for Adding New Project -->
    <a href="/project/new" class="btn btn-primary">Add New Project</a>

<?php include "Views/templates/footer.php"; ?>
