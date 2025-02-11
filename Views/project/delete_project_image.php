<?php

use App\Controllers\ProjectController;

$errors = [];// Initialize the $errors array

if (isset($_POST['image_id']) && isset($_POST['image_path'])) {
    $imageId = $_POST['image_id'];
    $imagePath = $_POST['image_path'];

    // Check if 'previousPage' is set
    if (isset($_POST['previousPage'])) {
        $previousPage = $_POST['previousPage'];
    }

    // Get image_id from the URL if not passed via POST
    if (!isset($image_id)) {
        $uri = $_SERVER['REQUEST_URI']; // Example: "/project/3/something"
        $segments = explode('/', trim($uri, '/'));

        if (isset($segments[1])) { // Ensure the second segment exists
            $image_id = $segments[1]; // Get the second segment as image_id
        }
    }

    // Validate the image ID
    if (!isset($image_id) || $imageId != $image_id) {
        $errors[] = 'Invalid image ID';

        if (!isset($previousPage)) {
            $errors[] = "Previous page not found";
            $previousPage = "/projects";
        }

        // Properly serialize the errors array before redirecting
        $queryString = http_build_query(['errors' => $errors]);  // This serializes the errors array
        header("Location: $previousPage?$queryString");
        exit;
    }

    // Perform the image deletion
    $projectController = new ProjectController();
    $row = $projectController->deleteImage($image_id);

    if ($row <= 0) {
        $errors[] = 'Image not deleted correctly :' . $image_id . " " . $row;

        if (!isset($previousPage)) {
            $errors[] = "Previous page not found";
            $previousPage = "/projects";
        }

        // Properly serialize the errors array before redirecting
        $queryString = http_build_query(['errors' => $errors]);
        header("Location: $previousPage?$queryString");
        exit;
    }

    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath; // Ensure you get the full server path
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            $success = 'Image deleted correctly';
        } else {
            $errors[] = 'Image not deleted from database but not server file , please contact an administrator if you want it correctly removed';
        }
    } else {
        $errors[] = 'Image not deleted from database but not server file , please contact an administrator if you want it correctly removed';
    }


    if (!isset($previousPage)) {
        $errors[] = "Previous page not found";
        $previousPage = "/projects";
    }

    // Serialize both success and errors before redirecting
    $queryString = http_build_query(['success' => $success, 'errors' => $errors]);
    header("Location: $previousPage?$queryString");
    exit;
}
?>
