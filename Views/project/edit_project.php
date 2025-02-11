<?php
use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;

$title = $description = $externalLink = $visibility = "";
$images = [];
$errors = [];
$success ="";

if (isset($_GET['errors'])) {
    $errors = $_GET['errors'];
}
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

if (!isset($projectId)) {
    $uri = $_SERVER['REQUEST_URI']; // Example: "/project/3/something"
    $segments = explode('/', trim($uri, '/'));

    if (isset($segments[1])) { // Ensure the second segment exists
        $projectId = $segments[1]; // Get the second segment
    }
}

include "Views/templates/header.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['csrf_token'])) {
    $error_message = "Please Log In First";
    header('Location: login.php?error_message=' . urlencode($error_message));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $projectController = new ProjectController();
    if (isset($projectId)) {
        try {
            $project = $projectController->getProject($projectId);

        } catch (Exception $e) {
            $errors[] ="Exception error while fetching project data : " . $e->getMessage();
            exit;
        }
    } else {
        $errors[] = "No Project Id given or Found in the URI ";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        $errors[] = "Sorry, you are not authorized to access this page";
    }

    if (!isset($_POST['project_id']) || $projectId !== $_POST['project_id']) {
        $errors[] = "Sorry, you are not authorized to modify this project";
    }

    $user_projectController = new User_ProjectController();
    $userId = $_SESSION['user_id'];

    if (!$user_projectController->isUserAllowedToUpdate($projectId, $userId)) {
        $errors[] = "Sorry, you are not authorized to modify this project";
    }

    if (empty($errors)) {
        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $externalLink = trim($_POST["externalLink"] ?? "");
        $visibility = trim($_POST["visibility"] ?? "");

        // Validate external link (if provided)
        if (!empty($externalLink) && !filter_var($externalLink, FILTER_VALIDATE_URL)) {
            $errors[] = "Invalid URL for external link.";
        }

        // Sanitize title and description
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

        // Ensure visibility is either 'public' or 'private'
        if (!in_array($visibility, ['public', 'private'])) {
            $errors[] = "Invalid visibility value.";
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/img/projects/';

        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION);
                    $date = date('Y-m-d_H-i-s');  // Format: YYYY-MM-DD_HH-MM-SS
                    $fileName = "projectId_{$projectId}_image_" . ($index + 1) . "_{$date}.$extension";
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $images[] = '/public/img/projects/' . $fileName; // Save the filepath for database reference
                    } else {
                        $errors[] = "Failed to upload " . htmlspecialchars($fileName);
                    }
                } else {
                    $errors[] = "Error uploading " . htmlspecialchars($_FILES['images']['name'][$index]);
                }
            }
        }

        if (!empty($errors)) {
            exit;
        }

        $projectController = new ProjectController();
        try {
            $modified = $projectController->modifyProject($projectId, $title, $description, $externalLink, $visibility, $images);
            if ($modified) {
                header("Location: /project/$projectId");
                exit;
            }
        } catch (Exception $e) {
            error_log("Project modification error: " . $e->getMessage());
            $errors[] = "An error occurred while modifying the project. Please try again later.";
        }
    }
}

?>

    <div class="container">
        <h2>Edit Project</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="color: red">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-danger" style="color: green">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">
                Project updated successfully!
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="project_id" value="<?= htmlspecialchars($projectId) ?>">

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($project->getTitle()) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control" required><?= htmlspecialchars($project->getDescription()) ?></textarea>
            </div>

            <div class="form-group">
                <label for="externalLink">External Link:</label>
                <input type="url" name="externalLink" id="externalLink" class="form-control" value="<?= htmlspecialchars($project->getExternalLink()) ?>" placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label for="visibility">Visibility:</label>
                <select name="visibility" id="visibility" class="form-control">
                    <option value="public" <?= ($project->getVisibilityStr() === "public") ? "selected" : "" ?>>Public</option>
                    <option value="private" <?= ($project->getVisibilityStr() === "private") ? "selected" : "" ?>>Private</option>
                </select>
            </div>

            <div class="form-group">
                <label for="images">Upload Images:</label>
                <input type="file" name="images[]" id="images" class="form-control" multiple>
                <div id="imagePreview" class="mt-3"></div>
            </div>

            <?php if (isset($projectId)): ?>
                <button type="submit" class="btn btn-primary">Update Project</button>
            <?php endif; ?>
        </form>
        <?php foreach ($project->getImages() as $image): ?>
            <div>
                <img src="<?php echo $image['image_path']; ?>" alt="Project Image" />
                <p>Uploaded on: <?php echo $image['uploaded_at']; ?></p>

                <!-- Delete button inside a form -->
                <form action="/deleteImg/<?php echo $image['id']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                    <input type="hidden" name="image_path" value="<?php echo $image['image_path']; ?>">
                    <input type="hidden" name="previousPage" value="<?php echo parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>">
                    <button type="submit">Delete Image From project</button>
                </form>

            </div>
        <?php endforeach; ?>


        <script>
            // Image Preview Function
            document.getElementById('images').addEventListener('change', function(event) {
                const files = event.target.files;
                const previewContainer = document.getElementById('imagePreview');

                // Check if previewContainer is found
                if (previewContainer) {
                    previewContainer.innerHTML = ''; // Clear previous previews

                    Array.from(files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;

                            // Check if style is applied correctly
                            if (img.style) {
                                img.style.maxWidth = '150px';
                                img.style.margin = '5px';
                            } else {
                                console.error("Style property is not accessible");
                            }

                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    console.error("Preview container not found.");
                }
            });
        </script>
    </div>

<?php include "Views/templates/footer.php"; ?>