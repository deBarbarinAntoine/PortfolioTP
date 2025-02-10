<?php

use App\Controllers\User_ProjectController;

if (!isset($projectID)) {
    $uri = $_SERVER['REQUEST_URI']; // Example: "/project/3/something"
    $segments = explode('/', trim($uri, '/'));

    if (isset($segments[1])) { // Ensure the second segment exists
        $projectID = $segments[1]; // Get the second segment
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);
    $projectId = htmlspecialchars($_POST['project_id']);

    if (isset($projectID)) {
        if ($projectId != $projectID ) {
            $errorMessage = "Failed to select correct project ID.";
            header("Location: /project/$projectId?error_message=" . $errorMessage);
            exit;
        }
    }

    // Validate email and role
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && in_array($role, ['contributor', 'viewer'])) {
        $user_projectController = new User_ProjectController();

        try {
            // Add the user to the project
           $success = $user_projectController->addUserToProject($email, $role, $projectId);
            if ($success <= 0) {
                $errorMessage = "User could not be added to this project. email = " . $email . " project = " . $projectId;
                header("Location: /project/$projectId?error_message=" . $errorMessage);
                exit;
            }

            // Redirect back to the project details page with a success message
            $successMessage = "User added successfully to the project!";
            header("Location: /project/$projectId?success_message=$successMessage");
            exit;
        } catch (Exception $e) {
            $errorMessage = "Error adding user to the project: " . $e->getMessage();
            header("Location: /project/$projectId?error_message=$errorMessage");
            exit;
        }
    } else {
        $errorMessage = 'Invalid email or role.';
        header("Location: /project/$projectId?error_message=$errorMessage");
        exit;
    }
}
?>