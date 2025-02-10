<?php
//  Form to add a new project.

if (!isset($_SESSION['user_id'])){
    $error_message = "Please Log In First";
    header('Location: login.php?error_message='. $error_message);
    exit;
}

include '../user/header.php';

$errorMessages = [];
$title = $description = $externalLink = $visibility = "";

use App\Controllers\ProjectController;

$projectController = new ProjectController();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
    $externalLink = isset($_POST["externalLink"]) ? trim($_POST["externalLink"]) : "";
    $visibility = isset($_POST["visibility"]) ? trim($_POST["visibility"]) : "";

    if (empty($title)) {
        $errorMessages[] = "Title is required.";
    }
    if (empty($description)) {
        $errorMessages[] = "Description is required.";
    }

    if (empty($errorMessages)) {
        $projectId = $projectController->createProject($title, $description, $externalLink, $visibility);
        if ($projectId <= 0) {
            $errorMessages[] = "Project Creation Failed";
        } else {
            header('Location: projects.php?id='. $projectId);
            exit;
        }
    }
}

?>

<h2>Add a New Project</h2>

<?php if (!empty($errorMessages)): ?>
    <div style="color: red;">
        <ul>
            <?php foreach ($errorMessages as $message): ?>
                <li><?php echo htmlspecialchars($message); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="" method="POST">
    <label for="title">Project Title:</label>
    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>
    <br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required><?php echo htmlspecialchars($description); ?></textarea>
    <br>

    <label for="externalLink">External Link:</label>
    <input type="url" name="externalLink" id="externalLink" value="<?php echo htmlspecialchars($externalLink); ?>">
    <br>

    <label for="visibility">Visibility:</label>
    <select name="visibility" id="visibility">
        <option value="public" <?php echo ($visibility == "public") ? "selected" : ""; ?>>Public</option>
        <option value="private" <?php echo ($visibility == "private") ? "selected" : ""; ?>>Private</option>
    </select>
    <br>

    <button type="submit">Create Project</button>
</form>

<?php include '../user/footer.php'; ?>
