<?php
// List of projects added by the logged-in user.

use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = (int)$_SESSION['user_id'];
$_SESSION['previousPage'] = "my_projects.php";


$projectController = new ProjectController();
$userProjectsController = new User_ProjectController();

$projectListId = $userProjectsController->getUserProject($user_id);

?>

<h1>User Projects</h1>

    <?php if (empty($projectListId)): ?>
        <p>No projects found for this user.</p>
    <?php else: ?>

        <div class="project-list">
            <?php foreach ($projectListId as $role => $projects): ?>
                <h2><?php echo ucfirst($role); ?> Projects</h2>

                <?php if (empty($projects)): ?>
                    <p>No projects for this role.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($projects as $project): ?>
                            <li>
                                <strong>Project Name:</strong>
                                <a href="project.php?name=<?php echo urlencode($project['name']); ?>&id=<?php echo urlencode($project['id']); ?>">
                                    <?php echo htmlspecialchars($project['name']); ?>
                                </a> <br>
                                <strong>Description:</strong> <?php echo htmlspecialchars($project['description']); ?> <br>
                                <strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?> <br>
                                <strong>Created At:</strong> <?php echo htmlspecialchars($project['created_at']); ?> <br>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
