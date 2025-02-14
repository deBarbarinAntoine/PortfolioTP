<?php

include "Views/templates/header.php"; ;

use App\Controllers\User_SkillController;

$user_id = $_SESSION['user_id'];
$user_csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userSkillController = new User_SkillController();
    $userSkillId = $_POST['userSkillId'];

    var_dump($userSkillId);

    $success = $userSkillController->deleteSkillFromUser($userSkillId);
    if ($success <= 0) {
        $error = "Failed to update user skill level";
        header("Location: /profile/skills?error_message=" . urlencode($error));
        exit;
    }
    $success_message = "User skill deleted successfully";
    header("Location: /profile/skills?success_message=" . urlencode($success_message));
    exit;

}
