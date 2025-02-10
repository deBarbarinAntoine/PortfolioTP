<?php

use App\Controllers\SkillController;

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /login");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed.");
}

$skillController = new SkillController();

// Handle Delete Skill
if (isset($ParamID)) {
    $skill_id = $ParamID;
    $success = $skillController->deleteSkill($skill_id);

    // Store the success message in the session if creation was successful
    if ($success > 0) {
        $_SESSION['success_message'] = "Skill deleted successfully! (Deleted Rows: $success)";
    } else {
        $_SESSION['error_message'] = "Failed to delete skill. (Affected Rows: $success) Please try again.";
    }

    header("Location: /admin/skills"); // Refresh the page to see the updated list
    exit();
}