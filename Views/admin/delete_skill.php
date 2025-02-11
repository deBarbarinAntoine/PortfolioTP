<?php

include "Views/templates/header.php";

use App\Controllers\SkillController;


// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /login");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: /login");
    exit();
}

if (!isset($skillId)) {
    $uri = $_SERVER['REQUEST_URI']; // Example: "/project/3/something"
    $segments = explode('/', trim($uri, '/'));

    if (isset($segments[2])) { // Ensure the second segment exists
        $skillId = $segments[2]; // Get the second segment
    }
}

$skillController = new SkillController();

// Handle Delete Skill
if (isset($skillId)) {

     $success = $skillController->deleteSkill($skillId);

    // Store the success message in the session if creation was successful
    if ($success > 0) {
        $success_message = "Skill deleted successfully! (Deleted Rows: $success)";
        header("Location: /admin/skills?success_message=" . $success_message);
        exit();
    } else {
        $error_message = "Failed to delete skill. (Affected Rows: $success) Please try again.";
        header("Location: /admin/skills?error_message=" . $error_message);
        exit();
    }


}
?>


<?php include "Views/templates/footer.php"; ?>
