<?php
use App\Controllers\ProjectController;
use App\Controllers\User_ProjectController;

$projectId = $ParamID ?? '';
$title = $description = $externalLink = $visibility = "";
$images = [];
$errors = [];

include "../user/header.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['csrf_token'])) {
    $error_message = "Please Log In First";
    header('Location: login.php?error_message=' . urlencode($error_message));
    exit;
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

        $uploadDir = '../../public/img/projects/';
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION);
                    $fileName = "projectId_{$projectId}_image_" . ($index + 1) . ".$extension";
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $images[] = $fileName; // Save the filename for database reference
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
                header('Location: project.php?id=' . $projectId);
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
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    <div class="container">
        <h2>Edit Project</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
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
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control" required><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div class="form-group">
                <label for="externalLink">External Link:</label>
                <input type="url" name="externalLink" id="externalLink" class="form-control" value="<?= htmlspecialchars($externalLink) ?>" placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label for="visibility">Visibility:</label>
                <select name="visibility" id="visibility" class="form-control">
                    <option value="public" <?= ($visibility === "public") ? "selected" : "" ?>>Public</option>
                    <option value="private" <?= ($visibility === "private") ? "selected" : "" ?>>Private</option>
                </select>
            </div>

            <div class="form-group">
                <label for="images">Upload Images:</label>
                <input type="file" name="images[]" id="images" class="form-control" multiple>
                <div id="imagePreview" class="mt-3"></div>
            </div>

            <button type="submit" class="btn btn-primary">Update Project</button>
        </form>

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

<?php include "../user/footer.php"; ?>