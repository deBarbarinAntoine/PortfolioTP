<?php

include "Views/templates/header.php"; ;

use App\Controllers\User_SkillController;

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}
$user_id = $_SESSION['user_id'];
$user_csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skill_id = $_POST['new_skill_id'];
    $skill_level = $_POST['new_skill_level'];

    $userSkillController = new User_SkillController();
    $userSkillId = $userSkillController->getUserSkills($user_id);

    $success =  $userSkillController->addSkillToUser($user_id, $skill_id,  $skill_level);

    if (is_string($success)) {
        $error = $success;
        header("Location: /profile/skills?error=$error");
        exit;
    }

    if ($success) {
        header("Location: /profile/skills?success=added skill successfully");
        exit;
    }

}
